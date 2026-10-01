<?php

namespace App\Http\Controllers;

use App\Models\AyudaEntregada;
use App\Models\Beneficiario;
use App\Models\NecesidadBeneficiario;
use App\Models\TipoAyuda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AyudaEntregadaController extends Controller
{
    /** Listado de ayudas entregadas (con filtros) */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $ayudas = AyudaEntregada::with(['beneficiario', 'tipoAyuda', 'usuario'])
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('beneficiario', function ($b) use ($q) {
                    $b->where(function ($w) use ($q) {
                        $w->where('dni', 'like', "%{$q}%")
                          ->orWhere('nombres', 'like', "%{$q}%")
                          ->orWhere('apellidos', 'like', "%{$q}%");
                    });
                });
            })
            ->when($request->filled('tipo_ayuda_id'), fn ($x) => $x->where('tipo_ayuda_id', $request->tipo_ayuda_id))
            ->when($request->filled('desde'), fn ($x) => $x->whereDate('fecha_entrega', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($x) => $x->whereDate('fecha_entrega', '<=', $request->hasta))
            ->orderByDesc('fecha_entrega')->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $tipos = TipoAyuda::orderBy('nombre')->get();

        return view('ayudas.index', compact('ayudas', 'tipos', 'q'));
    }

    public function create(Request $request)
    {
        return view('ayudas.create', [
            'beneficiarios' => Beneficiario::where('estado', 'activo')->orderBy('apellidos')->orderBy('nombres')->get(),
            'tipos' => TipoAyuda::where('activo', true)->orderBy('nombre')->get(),
            'seleccionado' => $request->input('beneficiario'),
        ]);
    }

    /** RF04: registrar ayuda entregada */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'beneficiario_id' => ['required', Rule::exists('beneficiarios', 'id')->where('estado', 'activo')],
            'tipo_ayuda_id' => ['required', Rule::exists('tipos_ayuda', 'id')->where('activo', true)],
            'fecha_entrega' => ['required', 'date', 'before_or_equal:today'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'observacion' => ['nullable', 'string', 'max:2000'],
        ], [
            'beneficiario_id.required' => 'Selecciona un beneficiario.',
            'beneficiario_id.exists' => 'El beneficiario no existe o está inactivo.',
            'tipo_ayuda_id.required' => 'Selecciona el tipo de ayuda.',
            'fecha_entrega.required' => 'Indica la fecha de entrega.',
            'fecha_entrega.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        DB::transaction(function () use ($datos, $request) {
            AyudaEntregada::create($datos + ['user_id' => $request->user()->id]);

            // Si el beneficiario tenía pendiente este tipo de ayuda, se marca como atendida.
            NecesidadBeneficiario::where('beneficiario_id', $datos['beneficiario_id'])
                ->where('tipo_ayuda_id', $datos['tipo_ayuda_id'])
                ->where('estado', 'pendiente')
                ->update(['estado' => 'atendida']);
        });

        $destino = $request->boolean('desde_ficha')
            ? route('beneficiarios.show', $datos['beneficiario_id'])
            : route('ayudas.index');

        return redirect($destino)->with('exito', 'Ayuda registrada correctamente.');
    }
}
