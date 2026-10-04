@extends('layouts.app')
@section('titulo', 'Categorías de producto')

@section('contenido')
<h2 class="mb-3">Categorías de producto</h2>
<div class="card shadow-sm mb-4"><div class="card-body">
    <form method="POST" action="{{ route('categorias.store') }}" class="row g-2">
        @csrf
        <div class="col-md-8">
            <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre de la categoría"
                   class="form-control @error('nombre') is-invalid @enderror" required>
            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4"><button class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i>Agregar</button></div>
    </form>
</div></div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Categoría</th><th class="text-center">Productos</th><th></th></tr></thead>
            <tbody>
            @foreach ($categorias as $c)
                <tr>
                    <td class="fw-semibold">{{ $c->nombre }}</td>
                    <td class="text-center">{{ $c->productos_count }}</td>
                    <td class="text-end">
                        @if (auth()->user()->tienePermiso('productos.eliminar'))
                            <form method="POST" action="{{ route('categorias.destroy', $c) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar esta categoría? Solo es posible si no tiene productos.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
