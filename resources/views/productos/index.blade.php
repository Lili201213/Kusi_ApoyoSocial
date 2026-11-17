<x-layout>
    <x-slot:title>{{ __('Inventario') }}</x-slot>

@php $u = auth()->user(); $fmt = fn ($n) => \App\Services\InventarioService::fmt($n); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">{{ __('Inventario') }}</h2>
    <div class="d-flex flex-wrap gap-2">
        @if ($u->tienePermiso('inventario.entrada'))
            <a href="{{ route('movimientos.entrada') }}" class="btn btn-success"><i class="bi bi-box-arrow-in-down me-1"></i>{{ __('Entrada') }}</a>
        @endif
        @if ($u->tienePermiso('inventario.salida'))
            <a href="{{ route('movimientos.salida') }}" class="btn btn-warning"><i class="bi bi-box-arrow-up me-1"></i>{{ __('Salida') }}</a>
        @endif
        @if ($u->tienePermiso('productos.crear'))
            <a href="{{ route('productos.create') }}" class="btn btn-outline-success"><i class="bi bi-plus-lg me-1"></i>{{ __('Nuevo producto') }}</a>
        @endif
    </div>
</div>

@if ($totalBajo > 0 && $filtro !== 'bajo')
    <div class="alert alert-warning py-2">
        <i class="bi bi-exclamation-triangle me-1"></i>
        {{ __('Hay :total producto(s) con stock bajo.', ['total' => $totalBajo]) }}
        <a href="{{ route('productos.index', ['filtro' => 'bajo']) }}" class="alert-link">{{ __('Ver cuáles') }}</a>
    </div>
@endif

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4"><input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="{{ __('Buscar producto') }}"></div>
    <div class="col-6 col-md-3">
        <select name="categoria_id" class="form-select">
            <option value="">{{ __('Todas las categorías') }}</option>
            @foreach ($categorias as $c)
                <option value="{{ $c->id }}" @selected(request('categoria_id') == $c->id)>{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <select name="filtro" class="form-select">
            <option value="" @selected($filtro === '')>{{ __('Activos') }}</option>
            <option value="bajo" @selected($filtro === 'bajo')>{{ __('Stock bajo') }}</option>
            <option value="inactivos" @selected($filtro === 'inactivos')>{{ __('Inactivos') }}</option>
        </select>
    </div>
    <div class="col-12 col-md-2 d-flex gap-1">
        <button class="btn btn-outline-success flex-fill" title="{{ __('Buscar') }}"><i class="bi bi-search"></i></button>
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary" title="{{ __('Limpiar') }}"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>{{ __('Producto') }}</th><th>{{ __('Categoría') }}</th><th class="text-end">{{ __('Stock') }}</th><th class="text-end">{{ __('Mínimo') }}</th><th>{{ __('Estado') }}</th><th class="text-end">{{ __('Acciones') }}</th></tr>
            </thead>
            <tbody>
            @forelse ($productos as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->nombre }}</td>
                    <td>{{ $p->categoria->nombre ?? '—' }}</td>
                    <td class="text-end">
                        <span class="badge {{ $p->stock_bajo ? 'text-bg-danger' : 'text-bg-success' }}">{{ $fmt($p->stock_actual) }} {{ $p->unidad_medida }}</span>
                    </td>
                    <td class="text-end text-muted">{{ $fmt($p->stock_minimo) }}</td>
                    <td><span class="badge {{ $p->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $p->activo ? __('Activo') : __('Inactivo') }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('productos.show', $p) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Ver movimientos') }}"><i class="bi bi-eye"></i></a>
                        @if ($u->tienePermiso('productos.crear'))
                            <a href="{{ route('productos.edit', $p) }}" class="btn btn-sm btn-outline-warning" title="{{ __('Editar') }}"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('productos.estado', $p) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary" title="{{ $p->activo ? __('Desactivar') : __('Activar') }}">
                                    <i class="bi {{ $p->activo ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i></button>
                            </form>
                        @endif
                        @if ($u->tienePermiso('productos.eliminar'))
                            <form method="POST" action="{{ route('productos.destroy', $p) }}" class="d-inline"
                                  onsubmit="return confirm('{{ __('¿Eliminar este producto? Solo es posible si nunca tuvo movimientos.') }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="{{ __('Eliminar') }}"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No hay productos con esos filtros.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $productos->links('pagination::bootstrap-5') }}</div>
</x-layout>
