<x-layout>
    <x-slot:title>{{ $producto->nombre }}</x-slot>

@php $u = auth()->user(); $fmt = fn ($n) => \App\Services\InventarioService::fmt($n); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h2 class="mb-0">{{ $producto->nombre }}</h2>
        <span class="text-muted">{{ $producto->categoria->nombre ?? __('Sin categoría') }}</span>
        <span class="badge {{ $producto->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $producto->activo ? __('Activo') : __('Inactivo') }}</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>{{ __('Volver') }}</a>
        @if ($producto->activo && $u->tienePermiso('inventario.entrada'))
            <a href="{{ route('movimientos.entrada', ['producto' => $producto->id]) }}" class="btn btn-success">{{ __('Entrada') }}</a>
        @endif
        @if ($producto->activo && $u->tienePermiso('inventario.salida'))
            <a href="{{ route('movimientos.salida', ['producto' => $producto->id]) }}" class="btn btn-warning">{{ __('Salida') }}</a>
        @endif
        @if ($u->tienePermiso('productos.crear'))
            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-outline-warning"><i class="bi bi-pencil me-1"></i>{{ __('Editar') }}</a>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2">
        <div class="text-muted small">{{ __('Stock actual') }}</div>
        <div class="fs-4 fw-bold {{ $producto->stock_bajo ? 'text-danger' : '' }}">{{ $fmt($producto->stock_actual) }} <small>{{ $producto->unidad_medida }}</small></div></div></div></div>
    <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2">
        <div class="text-muted small">{{ __('Stock mínimo') }}</div><div class="fs-4 fw-bold">{{ $fmt($producto->stock_minimo) }}</div></div></div></div>
    <div class="col-12 col-md-6"><div class="card shadow-sm"><div class="card-body py-2">
        <div class="text-muted small">{{ __('Descripción') }}</div>{{ $producto->descripcion ?: '—' }}</div></div></div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-semibold">{{ __('Historial de movimientos') }}</div>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>{{ __('Fecha') }}</th><th>{{ __('Tipo') }}</th><th class="text-end">{{ __('Cantidad') }}</th><th>{{ __('Motivo') }}</th><th>{{ __('Registró') }}</th></tr></thead>
            <tbody>
            @forelse ($movimientos as $m)
                <tr>
                    <td>{{ $m->fecha->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $m->tipo === 'entrada' ? 'text-bg-success' : 'text-bg-danger' }}">{{ $m->tipo === 'entrada' ? __('Entrada') : __('Salida') }}</span></td>
                    <td class="text-end">{{ $m->tipo === 'entrada' ? '+' : '−' }}{{ $fmt($m->cantidad) }}</td>
                    <td>
                        @if ($m->donacion_id)
                            <a href="{{ route('donaciones.show', $m->donacion_id) }}" class="text-decoration-none">{{ $m->motivo }}</a>
                        @else {{ $m->motivo ?: '—' }} @endif
                    </td>
                    <td>{{ $m->usuario->name }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-3">{{ __('Sin movimientos.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $movimientos->links('pagination::bootstrap-5') }}</div>
</x-layout>
