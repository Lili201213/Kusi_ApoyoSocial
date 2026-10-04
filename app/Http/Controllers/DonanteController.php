<?php

namespace App\Http\Controllers;

use App\Models\Donante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonanteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $donantes = Donante::withCount('donaciones')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombre_razon_social', 'like', "%{$q}%")->orWhere('documento', 'like', "%{$q}%");
                });
            })
            ->orderBy('nombre_razon_social')
            ->paginate(15)
            ->withQueryString();

        return view('donantes.index', compact('donantes', 'q'));
    }

    public function create(Request $request)
    {
        return view('donantes.create', [
            'donante' => new Donante(['tipo_persona' => 'natural']),
            'volver' => $request->input('volver'),
        ]);
    }

    public function store(Request $request)
    {
        Donante::create($this->validar($request));

        if ($request->input('volver') === 'donacion') {
            return redirect()->route('donaciones.create')->with('exito', 'Donante registrado. Ya puedes seleccionarlo.');
        }

        return redirect()->route('donantes.index')->with('exito', 'Donante registrado correctamente.');
    }

    public function edit(Donante $donante)
    {
        return view('donantes.edit', ['donante' => $donante, 'volver' => null]);
    }

    public function update(Request $request, Donante $donante)
    {
        $donante->update($this->validar($request, $donante->id));

        return redirect()->route('donantes.index')->with('exito', 'Donante actualizado.');
    }

    /** Solo si no tiene donaciones */
    public function destroy(Donante $donante)
    {
        $n = $donante->donaciones()->count();

        if ($n > 0) {
            return back()->with('error', "No se puede eliminar a «{$donante->nombre_razon_social}»: tiene {$n} donación(es) registradas.");
        }

        $donante->delete();

        return back()->with('exito', 'Donante eliminado.');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'tipo_persona' => ['required', 'in:natural,juridica'],
            'nombre_razon_social' => ['required', 'string', 'max:150'],
            'documento' => ['nullable', 'string', 'max:20', Rule::unique('donantes', 'documento')->ignore($id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ], [
            'nombre_razon_social.required' => 'El nombre o razón social es obligatorio.',
            'documento.unique' => 'Ya existe un donante con ese documento.',
            'email.email' => 'El correo no tiene un formato válido.',
        ]);
    }
}
