<?php

namespace App\Http\Controllers;

use App\Models\NecesidadBeneficiario;
use App\Models\TipoAyuda;
use Illuminate\Http\Request;

class TipoAyudaController extends Controller
{
    public function index()
    {
        $tipos = TipoAyuda::withCount('ayudas')->orderBy('nombre')->get();

        return view('tipos_ayuda.index', compact('tipos'));
    }

    /** Registrar un tipo de ayuda (catálogo) */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:tipos_ayuda,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], [
            'nombre.required' => __('El nombre es obligatorio.'),
            'nombre.unique' => __('Ya existe un tipo de ayuda con ese nombre.'),
        ]);

        TipoAyuda::create($datos + ['activo' => true]);

        return back()->with('exito', __('Tipo de ayuda registrado.'));
    }

    /** Activar / desactivar (no se borra para no perder historial) */
    public function cambiarEstado(TipoAyuda $tipoAyuda)
    {
        $tipoAyuda->update(['activo' => ! $tipoAyuda->activo]);

        return back()->with('exito', __('Estado actualizado.'));
    }

    /** Eliminar solo si nunca se usó; si se usó, hay que desactivarlo */
    public function destroy(TipoAyuda $tipoAyuda)
    {
        $usos = $tipoAyuda->ayudas()->count()
            + NecesidadBeneficiario::where('tipo_ayuda_id', $tipoAyuda->id)->count();

        if ($usos > 0) {
            return back()->with('error', __('No se puede eliminar «:nombre»: está en uso (:usos registro(s)). Puedes desactivarlo para que no se ofrezca más.', [
                'nombre' => $tipoAyuda->nombre,
                'usos' => $usos,
            ]));
        }

        $tipoAyuda->delete();

        return back()->with('exito', __('Tipo de ayuda eliminado.'));
    }
}
