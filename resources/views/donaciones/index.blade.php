<x-layout>
    <x-slot:title>{{ __('Donaciones') }}</x-slot>

@php $u = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">{{ __('Donaciones') }}</h2>
    @if ($u->tienePermiso('donaciones.crear'))
        <a href="{{ route('donaciones.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>{{ __('Registrar donación') }}</a>
    @endif
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4"><input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="{{ __('Donante') }}"></div>
    <div class="col-6 col-md-2">
        <select name="estado" class="form-select">
            <option value="">{{ __('Todas') }}</option>
            <option value="registrada" @selected(request('estado') === 'registrada')>{{ __('Registradas') }}</option>
            <option value="anulada" @selected(request('estado') === 'anulada')>{{ __('Anuladas') }}</option>
        </select>
    </div>
    <div class="col-6 col-md-2"><input type="date" name="desde" value="{{ request('desde') }}" class="form-control" title="{{ __('Desde') }}"></div>
    <div class="col-6 col-md-2"><input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" title="{{ __('Hasta') }}"></div>
    <div class="col-6 col-md-2 d-flex gap-1">
        <button class="btn btn-outline-success flex-fill" title="{{ __('Buscar') }}"><i class="bi bi-search"></i></button>
        <a href="{{ route('donaciones.index') }}" class="btn btn-outline-secondary" title="{{ __('Limpiar') }}"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>{{ __('N.º') }}</th><th>{{ __('Fecha') }}</th><th>{{ __('Donante') }}</th><th class="text-center">{{ __('Productos') }}</th><th>{{ __('Estado') }}</th><th>{{ __('Registró') }}</th><th></th></tr></thead>
            <tbody>
            @forelse ($donaciones as $d)
                <tr>
                    <td>#{{ $d->id }}</td>
                    <td class="text-nowrap">{{ $d->fecha_donacion->format('d/m/Y') }}</td>
                    <td>{{ $d->donante->nombre_razon_social }}</td>
                    <td class="text-center">{{ $d->detalles_count }}</td>
                    <td><span class="badge {{ $d->estaAnulada() ? 'text-bg-secondary' : 'text-bg-success' }}">{{ $d->estaAnulada() ? __('Anulada') : __('Registrada') }}</span></td>
                    <td>{{ $d->usuario->name }}</td>
                    <td class="text-end"><a href="{{ route('donaciones.show', $d) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Ver detalle') }}"><i class="bi bi-eye"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No hay donaciones con esos filtros.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $donaciones->links('pagination::bootstrap-5') }}</div>
</x-layout>
