<?php

namespace App\Http\Controllers;

use App\Exceptions\StockInsuficienteException;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MovimientoInventarioController extends Controller
{
    public function __construct(private InventarioService $inventario)
    {
    }

    public function index(Request $request)
    {
        $movimientos = MovimientoInventario::with(['producto', 'usuario', 'donacion'])
            ->when($request->filled('producto_id'), fn ($x) => $x->where('producto_id', $request->producto_id))
            ->when(in_array($request->input('tipo'), ['entrada', 'salida']), fn ($x) => $x->where('tipo', $request->tipo))
            ->when($request->filled('desde'), fn ($x) => $x->whereDate('fecha', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($x) => $x->whereDate('fecha', '<=', $request->hasta))
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('movimientos.index', [
            'movimientos' => $movimientos,
            'productos' => Producto::orderBy('nombre')->get(),
        ]);
    }

    /** $tipo llega desde la ruta ('entrada' o 'salida'), nunca desde el usuario */
    public function create(Request $request, string $tipo)
    {
        return view('movimientos.create', [
            'tipo' => $tipo,
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(),
            'seleccionado' => $request->input('producto'),
        ]);
    }

    /** RF12 + RF13 */
    public function store(Request $request, string $tipo)
    {
        $datos = $request->validate([
            'producto_id' => ['required', Rule::exists('productos', 'id')->where('activo', true)],
            'cantidad' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'motivo' => [$tipo === 'salida' ? 'required' : 'nullable', 'string', 'max:255'],
        ], [
            'producto_id.required' => 'Selecciona un producto.',
            'producto_id.exists' => 'El producto no existe o está inactivo.',
            'cantidad.required' => 'Indica la cantidad.',
            'cantidad.min' => 'La cantidad debe ser mayor que 0.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'motivo.required' => 'Indica el motivo de la salida.',
        ]);

        try {
            $this->inventario->mover(
                (int) $datos['producto_id'], $tipo, (float) $datos['cantidad'],
                $request->user()->id, $datos['fecha'], $datos['motivo'] ?? null
            );
        } catch (StockInsuficienteException $e) {
            return back()->withInput()->withErrors(['cantidad' => $e->getMessage()]);
        }

        return redirect()->route('movimientos.index')
            ->with('exito', $tipo === 'entrada' ? 'Entrada registrada y stock actualizado.' : 'Salida registrada y stock actualizado.');
    }
}
