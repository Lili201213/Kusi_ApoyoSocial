<?php

namespace App\Http\Controllers;

use App\Exceptions\StockInsuficienteException;
use App\Models\Donacion;
use App\Models\Donante;
use App\Models\Producto;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DonacionController extends Controller
{
    public function __construct(private InventarioService $inventario)
    {
    }

    /** RF09: consultar donaciones */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $donaciones = Donacion::with(['donante', 'usuario'])->withCount('detalles')
            ->when($q !== '', fn ($x) => $x->whereHas('donante', fn ($d) => $d->where('nombre_razon_social', 'like', "%{$q}%")))
            ->when(in_array($request->input('estado'), ['registrada', 'anulada']), fn ($x) => $x->where('estado', $request->estado))
            ->when($request->filled('desde'), fn ($x) => $x->whereDate('fecha_donacion', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($x) => $x->whereDate('fecha_donacion', '<=', $request->hasta))
            ->orderByDesc('fecha_donacion')->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('donaciones.index', compact('donaciones', 'q'));
    }

    public function create()
    {
        return view('donaciones.create', [
            'donantes' => Donante::orderBy('nombre_razon_social')->get(),
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    /** RF07 + RF08: registrar donación; cada línea es una ENTRADA al inventario (RF13) */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'donante_id' => ['required', 'exists:donantes,id'],
            'fecha_donacion' => ['required', 'date', 'before_or_equal:today'],
            'observacion' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'distinct', Rule::exists('productos', 'id')->where('activo', true)],
            'items.*.cantidad' => ['required', 'numeric', 'min:0.01', 'max:999999'],
        ], [
            'donante_id.required' => 'Selecciona el donante.',
            'fecha_donacion.before_or_equal' => 'La fecha no puede ser futura.',
            'items.required' => 'Agrega al menos un producto.',
            'items.*.producto_id.required' => 'Selecciona el producto en todas las filas.',
            'items.*.producto_id.distinct' => 'No repitas el mismo producto: suma las cantidades en una sola fila.',
            'items.*.producto_id.exists' => 'Hay un producto que no existe o está inactivo.',
            'items.*.cantidad.required' => 'Indica la cantidad en todas las filas.',
            'items.*.cantidad.min' => 'Las cantidades deben ser mayores que 0.',
        ]);

        $donacion = DB::transaction(function () use ($datos, $request) {
            $donacion = Donacion::create([
                'donante_id' => $datos['donante_id'],
                'user_id' => $request->user()->id,
                'fecha_donacion' => $datos['fecha_donacion'],
                'estado' => 'registrada',
                'observacion' => $datos['observacion'] ?? null,
            ]);

            foreach ($datos['items'] as $item) {
                $donacion->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                ]);

                $this->inventario->mover(
                    (int) $item['producto_id'], 'entrada', (float) $item['cantidad'],
                    $request->user()->id, $datos['fecha_donacion'],
                    "Donación #{$donacion->id}", ['donacion_id' => $donacion->id]
                );
            }

            return $donacion;
        });

        return redirect()->route('donaciones.show', $donacion)
            ->with('exito', 'Donación registrada y stock actualizado.');
    }

    public function show(Donacion $donacion)
    {
        $donacion->load(['donante', 'usuario', 'anuladaPor', 'detalles.producto']);

        return view('donaciones.show', compact('donacion'));
    }

    /** Las donaciones no se borran: se anulan y se revierte el stock */
    public function anular(Request $request, Donacion $donacion)
    {
        $datos = $request->validate([
            'motivo_anulacion' => ['required', 'string', 'max:255'],
        ], ['motivo_anulacion.required' => 'Indica el motivo de la anulación.']);

        try {
            $hecho = DB::transaction(function () use ($donacion, $datos, $request) {
                $actual = Donacion::lockForUpdate()->findOrFail($donacion->id);

                if ($actual->estaAnulada()) {
                    return false;
                }

                foreach ($actual->detalles as $d) {
                    $this->inventario->mover(
                        $d->producto_id, 'salida', (float) $d->cantidad,
                        $request->user()->id, now()->toDateString(),
                        "Anulación de donación #{$actual->id}", ['donacion_id' => $actual->id]
                    );
                }

                $actual->update([
                    'estado' => 'anulada',
                    'motivo_anulacion' => $datos['motivo_anulacion'],
                    'anulada_at' => now(),
                    'anulada_por' => $request->user()->id,
                ]);

                return true;
            });
        } catch (StockInsuficienteException $e) {
            return back()->with('error', 'No se puede anular: '.$e->getMessage().' Parte de lo donado ya salió del inventario.');
        }

        return $hecho
            ? back()->with('exito', 'Donación anulada y stock revertido.')
            : back()->with('error', 'La donación ya estaba anulada.');
    }
}
