<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use Illuminate\Http\Request;

class CategoriaProductoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaProducto::withCount('productos')->orderBy('nombre')->get();

        return view('categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:categorias_producto,nombre'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        CategoriaProducto::create($datos);

        return back()->with('exito', 'Categoría registrada.');
    }

    /** Solo si no tiene productos */
    public function destroy(CategoriaProducto $categoria)
    {
        $n = $categoria->productos()->count();

        if ($n > 0) {
            return back()->with('error', "No se puede eliminar «{$categoria->nombre}»: tiene {$n} producto(s). Cámbialos de categoría primero.");
        }

        $categoria->delete();

        return back()->with('exito', 'Categoría eliminada.');
    }
}
