<x-layout>
    <x-slot:title>{{ __('Donantes') }}</x-slot>

@php $u = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">{{ __('Donantes') }}</h2>
    @if ($u->tienePermiso('donaciones.crear'))
        <a href="{{ route('donantes.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>{{ __('Nuevo donante') }}</a>
    @endif
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-8"><input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="{{ __('Buscar por nombre o documento') }}"></div>
    <div class="col-12 col-md-4 d-flex gap-1">
        <button class="btn btn-outline-success flex-fill"><i class="bi bi-search me-1"></i>{{ __('Buscar') }}</button>
        <a href="{{ route('donantes.index') }}" class="btn btn-outline-secondary" title="{{ __('Limpiar') }}"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>{{ __('Nombre / Razón social') }}</th><th>{{ __('Tipo') }}</th><th>{{ __('Documento') }}</th><th>{{ __('Teléfono') }}</th><th class="text-center">{{ __('Donaciones') }}</th><th class="text-end">{{ __('Acciones') }}</th></tr></thead>
            <tbody>
            @forelse ($donantes as $d)
                <tr>
                    <td class="fw-semibold">{{ $d->nombre_razon_social }}</td>
                    <td>{{ $d->tipo_persona === 'juridica' ? __('Jurídica') : __('Natural') }}</td>
                    <td>{{ $d->documento ?: '—' }}</td>
                    <td>{{ $d->telefono ?: '—' }}</td>
                    <td class="text-center">{{ $d->donaciones_count }}</td>
                    <td class="text-end text-nowrap">
                        @if ($u->tienePermiso('donaciones.crear'))
                            <a href="{{ route('donantes.edit', $d) }}" class="btn btn-sm btn-outline-warning" title="{{ __('Editar') }}"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if ($u->tienePermiso('donantes.eliminar'))
                            <form method="POST" action="{{ route('donantes.destroy', $d) }}" class="d-inline"
                                  onsubmit="return confirm(@js(__('¿Eliminar este donante? Solo es posible si no tiene donaciones.')))">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="{{ __('Eliminar') }}"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No hay donantes registrados.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $donantes->links('pagination::bootstrap-5') }}</div>
</x-layout>
