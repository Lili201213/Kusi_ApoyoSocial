<x-layout>
    <x-slot:title>{{ __('Alimentos / almuerzos') }}</x-slot>

@php $u = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">{{ __('Alimentos / almuerzos') }}</h2>
    @if ($u->tienePermiso('alimentos.crear'))
        <a href="{{ route('alimentos.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>{{ __('Registrar entrega') }}</a>
    @endif
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="{{ __('Beneficiario (DNI o nombre)') }}">
    </div>
    <div class="col-12 col-md-2">
        <select name="tipo_entrega" class="form-select">
            <option value="">{{ __('Todos los tipos') }}</option>
            @foreach ($tipos as $t)
                <option value="{{ $t }}" @selected(request('tipo_entrega') === $t)>{{ __($t === 'almuerzo' ? 'Almuerzo' : ($t === 'desayuno' ? 'Desayuno' : ($t === 'cena' ? 'Cena' : ($t === 'canasta' ? 'Canasta' : ucfirst($t))))) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2"><input type="date" name="desde" value="{{ request('desde') }}" class="form-control" title="{{ __('Desde') }}"></div>
    <div class="col-6 col-md-2"><input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" title="{{ __('Hasta') }}"></div>
    <div class="col-12 col-md-2 d-flex gap-1">
        <button class="btn btn-outline-success flex-fill" title="{{ __('Buscar') }}"><i class="bi bi-search"></i></button>
        <a href="{{ route('alimentos.index') }}" class="btn btn-outline-secondary" title="{{ __('Limpiar') }}"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2">
        <div class="text-muted small">{{ __('Entregas (según filtro)') }}</div><div class="fs-4 fw-bold">{{ $totalEntregas }}</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2">
        <div class="text-muted small">{{ __('Raciones (según filtro)') }}</div><div class="fs-4 fw-bold">{{ $totalRaciones }}</div></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>{{ __('Fecha') }}</th><th>{{ __('Beneficiario') }}</th><th>{{ __('Tipo') }}</th><th class="text-center">{{ __('Raciones') }}</th><th>{{ __('Registró') }}</th>@if ($u->tienePermiso('alimentos.eliminar'))<th></th>@endif</tr>
            </thead>
            <tbody>
            @forelse ($entregas as $e)
                <tr>
                    <td class="text-nowrap">{{ $e->fecha->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('beneficiarios.show', $e->beneficiario) }}" class="text-decoration-none">
                            {{ $e->beneficiario->apellidos }}, {{ $e->beneficiario->nombres }}
                        </a>
                        <div class="text-muted small">{{ $e->beneficiario->dni }}</div>
                    </td>
                    <td><span class="badge text-bg-info">{{ __($e->tipo_entrega === 'almuerzo' ? 'Almuerzo' : ($e->tipo_entrega === 'desayuno' ? 'Desayuno' : ($e->tipo_entrega === 'cena' ? 'Cena' : ($e->tipo_entrega === 'canasta' ? 'Canasta' : ucfirst($e->tipo_entrega))))) }}</span></td>
                    <td class="text-center">{{ $e->raciones }}</td>
                    <td>{{ $e->usuario->name }}</td>
                    @if ($u->tienePermiso('alimentos.eliminar'))
                    <td class="text-end">
                        @if ($u->tienePermiso('alimentos.eliminar'))
                            <form method="POST" action="{{ route('alimentos.destroy', $e) }}" class="d-inline"
                                  onsubmit="return confirm('{{ __('¿Eliminar este registro de entrega?') }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="{{ __('Eliminar') }}"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No hay entregas registradas con esos filtros.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $entregas->links('pagination::bootstrap-5') }}</div>
</x-layout>
