<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\EntregaAlimento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntregaAlimentoController extends Controller
{
    public const TIPOS = ['almuerzo', 'desayuno', 'cena', 'canasta', 'otro'];

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $base = EntregaAlimento::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('beneficiario', function ($b) use ($q) {
                    $b->where(function ($w) use ($q) {
                        $w->where('dni', 'like', "%{$q}%")
                          ->orWhere('nombres', 'like', "%{$q}%")
                          ->orWhere('apellidos', 'like', "%{$q}%");
                    });
                });
            })
            ->when(in_array($request->input('tipo_entrega'), self::TIPOS), fn ($x) => $x->where('tipo_entrega', $request->tipo_entrega))
            ->when($request->filled('desde'), fn ($x) => $x->whereDate('fecha', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($x) => $x->whereDate('fecha', '<=', $request->hasta));

        // Totales sobre el filtro aplicado (antes de ordenar/paginar)
        $totalEntregas = (clone $base)->count();
        $totalRaciones = (int) (clone $base)->sum('raciones');

        $entregas = $base->with(['beneficiario', 'usuario'])
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('alimentos.index', [
            'entregas' => $entregas,
            'tipos' => self::TIPOS,
            'q' => $q,
            'totalEntregas' => $totalEntregas,
            'totalRaciones' => $totalRaciones,
        ]);
    }

    public function create(Request $request)
    {
        return view('alimentos.create', [
            'beneficiarios' => Beneficiario::where('estado', 'activo')->orderBy('apellidos')->orderBy('nombres')->get(),
            'tipos' => self::TIPOS,
            'seleccionado' => $request->input('beneficiario'),
        ]);
    }

    /** RF05: registrar entrega de alimentos / almuerzos */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'beneficiario_id' => ['required', Rule::exists('beneficiarios', 'id')->where('estado', 'activo')],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'tipo_entrega' => ['required', Rule::in(self::TIPOS)],
            'raciones' => ['required', 'integer', 'min:1', 'max:50'],
            'observacion' => ['nullable', 'string', 'max:255'],
        ], [
            'beneficiario_id.required' => 'Selecciona un beneficiario.',
            'beneficiario_id.exists' => 'El beneficiario no existe o está inactivo.',
            'fecha.required' => 'Indica la fecha.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'tipo_entrega.required' => 'Selecciona el tipo de entrega.',
            'raciones.required' => 'Indica la cantidad de raciones.',
            'raciones.min' => 'Debe ser al menos 1.',
        ]);

        EntregaAlimento::create($datos + ['user_id' => $request->user()->id]);

        // "Guardar y registrar otro": conserva fecha, tipo y raciones para agilizar el registro diario
        if ($request->boolean('otro')) {
            return redirect()->route('alimentos.create')
                ->withInput($request->only('fecha', 'tipo_entrega', 'raciones'))
                ->with('exito', 'Entrega registrada. Puedes registrar otra.');
        }

        $destino = $request->boolean('desde_ficha')
            ? route('beneficiarios.show', $datos['beneficiario_id'])
            : route('alimentos.index');

        return redirect($destino)->with('exito', 'Entrega registrada correctamente.');
    }
}
