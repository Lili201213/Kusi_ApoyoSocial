<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\NecesidadBeneficiario;
use App\Models\TipoAyuda;
use Illuminate\Http\Request;

class BeneficiarioController extends Controller
{
    /** RF02: listado con búsqueda y filtro */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $estado = $request->input('estado');

        $beneficiarios = Beneficiario::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('dni', 'like', "%{$q}%")
                      ->orWhere('nombres', 'like', "%{$q}%")
                      ->orWhere('apellidos', 'like', "%{$q}%");
                });
            })
            ->when(in_array($estado, ['activo', 'inactivo']), fn ($query) => $query->where('estado', $estado))
            ->orderBy('apellidos')->orderBy('nombres')
            ->paginate(10)
            ->withQueryString();

        return view('beneficiarios.index', compact('beneficiarios', 'q', 'estado'));
    }

    /** RF01: formulario de registro */
    public function create()
    {
        return view('beneficiarios.create', ['beneficiario' => new Beneficiario(['num_familiares' => 1])]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['registrado_por'] = $request->user()->id;
        $datos['estado'] = 'activo';

        $beneficiario = Beneficiario::create($datos);

        return redirect()->route('beneficiarios.show', $beneficiario)
            ->with('exito', 'Beneficiario registrado correctamente.');
    }

    /** Ficha del beneficiario + RF03 (necesidades) + RF06 (historial) */
    public function show(Beneficiario $beneficiario)
    {
        $necesidades = $beneficiario->necesidades()->with('tipoAyuda')->orderByDesc('fecha_registro')->get();
        $ayudas = $beneficiario->ayudas()->with(['tipoAyuda', 'usuario'])->orderByDesc('fecha_entrega')->get();
        $alimentos = $beneficiario->entregasAlimentos()->with('usuario')->orderByDesc('fecha')->get();
        $tiposAyuda = TipoAyuda::where('activo', true)->orderBy('nombre')->get();

        return view('beneficiarios.show', compact('beneficiario', 'necesidades', 'ayudas', 'alimentos', 'tiposAyuda'));
    }

    /** RF02: modificar */
    public function edit(Beneficiario $beneficiario)
    {
        return view('beneficiarios.edit', compact('beneficiario'));
    }

    public function update(Request $request, Beneficiario $beneficiario)
    {
        $datos = $this->validar($request, $beneficiario->id);
        $beneficiario->update($datos);

        return redirect()->route('beneficiarios.show', $beneficiario)
            ->with('exito', 'Datos actualizados correctamente.');
    }

    /** RF03: registrar el tipo de ayuda que necesita */
    public function storeNecesidad(Request $request, Beneficiario $beneficiario)
    {
        $datos = $request->validate([
            'tipo_ayuda_id' => ['required', 'exists:tipos_ayuda,id'],
            'fecha_registro' => ['required', 'date'],
            'observacion' => ['nullable', 'string', 'max:255'],
        ], [
            'tipo_ayuda_id.required' => 'Selecciona el tipo de ayuda.',
            'fecha_registro.required' => 'Indica la fecha.',
        ]);

        $beneficiario->necesidades()->create($datos + ['estado' => 'pendiente']);

        return back()->with('exito', 'Necesidad registrada.');
    }

    public function atenderNecesidad(NecesidadBeneficiario $necesidad)
    {
        $necesidad->update(['estado' => 'atendida']);

        return back()->with('exito', 'Necesidad marcada como atendida.');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'dni' => ['required', 'digits:8', 'unique:beneficiarios,dni'.($id ? ",{$id}" : '')],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'sexo' => ['nullable', 'in:M,F,Otro'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'num_familiares' => ['required', 'integer', 'min:1', 'max:30'],
            'situacion' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'estado' => ['sometimes', 'in:activo,inactivo'],
        ], [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique' => 'Ya existe un beneficiario con ese DNI.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'num_familiares.required' => 'Indica el número de familiares.',
            'num_familiares.min' => 'Debe ser al menos 1.',
        ]);
    }
}
