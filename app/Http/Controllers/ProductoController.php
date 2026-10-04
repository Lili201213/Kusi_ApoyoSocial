<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\DetalleDonacion;
use App\Models\Producto;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public const UNIDADES = ['unidad', 'kg', 'gramo', 'litro', 'caja', 'bolsa', 'paquete', 'lata', 'docena', 'par'];

    public function __construct(private InventarioService $inventario)
    {
    }

    /** RF11: existencias disponibles */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $filtro = (string) $request->input('filtro', '');

        $productos = Producto::with('categoria')
            ->when($q !== '', fn ($x) => $x->where('nombre', 'like', "%{$q}%"))
            ->when($request->filled('categoria_id'), fn ($x) => $x->where('categoria_id', $request->categoria_id))
            ->when(in_array($filtro, ['', 'bajo']), fn ($x) => $x->where('activo', true))
            ->when($filtro === 'bajo', fn ($x) => $x->whereColumn('stock_actual', '<=', 'stock_minimo'))
            ->when($filtro === 'inactivos', fn ($x) => $x->where('activo', false))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('productos.index', [
            'productos' => $productos,
            'categorias' => CategoriaProducto::orderBy('nombre')->get(),
            'q' => $q,
            'filtro' => $filtro,
            'totalBajo' => Producto::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
        ]);
    }

    /** Ficha del producto + historial de movimientos */
    public function show(Producto $producto)
    {
        $movimientos = $producto->movimientos()->with(['usuario', 'donacion'])
            ->orderByDesc('fecha')->orderByDesc('id')->paginate(15);

        return view('productos.show', compact('producto', 'movimientos'));
    }

    public function create()
    {
        return view('productos.create', [
            'producto' => new Producto(['unidad_medida' => 'unidad', 'stock_minimo' => 0]),
            'categorias' => CategoriaProducto::orderBy('nombre')->get(),
            'unidades' => self::UNIDADES,
        ]);
    }

    /** RF10: registrar producto. El stock inicial entra como un movimiento. */
    public function store(Request $request)
    {
        $datos = $this->validar($request, null, [
            'stock_inicial' => ['nullable', 'numeric', 'min:0', 'max:999999'],
        ]);
        $inicial = (float) ($datos['stock_inicial'] ?? 0);
        unset($datos['stock_inicial']);

        $producto = DB::transaction(function () use ($datos, $inicial, $request) {
            $producto = Producto::create($datos + ['stock_actual' => 0, 'activo' => true]);

            if ($inicial > 0) {
                $this->inventario->mover($producto->id, 'entrada', $inicial, $request->user()->id, now()->toDateString(), 'Stock inicial');
            }

            return $producto;
        });

        return redirect()->route('productos.show', $producto)->with('exito', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', [
            'producto' => $producto,
            'categorias' => CategoriaProducto::orderBy('nombre')->get(),
            'unidades' => self::UNIDADES,
        ]);
    }

    /** El stock NO se edita aquí: solo cambia con movimientos. */
    public function update(Request $request, Producto $producto)
    {
        $producto->update($this->validar($request, $producto->id));

        return redirect()->route('productos.show', $producto)->with('exito', 'Producto actualizado.');
    }

    public function cambiarEstado(Producto $producto)
    {
        $producto->update(['activo' => ! $producto->activo]);

        return back()->with('exito', 'Estado actualizado.');
    }

    /** Solo si nunca tuvo movimientos ni donaciones */
    public function destroy(Producto $producto)
    {
        $n = $producto->movimientos()->count() + DetalleDonacion::where('producto_id', $producto->id)->count();

        if ($n > 0) {
            return back()->with('error', "No se puede eliminar «{$producto->nombre}»: tiene {$n} registro(s) de movimientos o donaciones. Puedes desactivarlo.");
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('exito', 'Producto eliminado.');
    }

    private function validar(Request $request, ?int $id, array $extra = []): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('productos', 'nombre')->ignore($id)],
            'categoria_id' => ['nullable', 'exists:categorias_producto,id'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'unidad_medida' => ['required', 'string', 'max:30'],
            'stock_minimo' => ['required', 'numeric', 'min:0', 'max:999999'],
        ] + $extra, [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con ese nombre.',
            'stock_minimo.required' => 'Indica el stock mínimo (puede ser 0).',
            'stock_inicial.min' => 'El stock inicial no puede ser negativo.',
        ]);
    }
}
