<x-layout>
    <x-slot:title>{{ __('Beneficiarios') }}</x-slot>

@php $u = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">{{ __('Beneficiarios') }}</h2>
    @if ($u->tienePermiso('beneficiarios.crear'))
        <a href="{{ route('beneficiarios.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i>{{ __('Nuevo beneficiario') }}
        </a>
    @endif
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-6">
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="{{ __('Buscar por DNI, nombres o apellidos') }}">
    </div>
    <div class="col-6 col-md-3">
        <select name="estado" class="form-select">
            <option value="">{{ __('Todos los estados') }}</option>
            <option value="activo" @selected($estado === 'activo')>{{ __('Activos') }}</option>
            <option value="inactivo" @selected($estado === 'inactivo')>{{ __('Inactivos') }}</option>
        </select>
    </div>
    <div class="col-6 col-md-3 d-flex gap-2">
        <button class="btn btn-outline-success flex-fill"><i class="bi bi-search me-1"></i>{{ __('Buscar') }}</button>
        <a href="{{ route('beneficiarios.index') }}" class="btn btn-outline-secondary" title="{{ __('Limpiar') }}"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('DNI') }}</th><th>{{ __('Nombre completo') }}</th><th>{{ __('Teléfono') }}</th>
                    <th class="text-center">{{ __('Familiares') }}</th><th>{{ __('Estado') }}</th><th class="text-end">{{ __('Acciones') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($beneficiarios as $b)
                <tr>
                    <td>{{ $b->dni }}</td>
                    <td>{{ $b->apellidos }}, {{ $b->nombres }}</td>
                    <td>{{ $b->telefono ?: '—' }}</td>
                    <td class="text-center">{{ $b->num_familiares }}</td>
                    <td>
                        <span class="badge {{ $b->estado === 'activo' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $b->estado === 'activo' ? __('Activo') : __('Inactivo') }}</span>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('beneficiarios.show', $b) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Ver ficha') }}"><i class="bi bi-eye"></i></a>
                        @if ($u->tienePermiso('beneficiarios.editar'))
                            <a href="{{ route('beneficiarios.edit', $b) }}" class="btn btn-sm btn-outline-warning" title="{{ __('Editar') }}"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if ($u->tienePermiso('beneficiarios.eliminar'))
                            <form method="POST" action="{{ route('beneficiarios.destroy', $b) }}" class="d-inline"
                                  onsubmit="return confirm('{{ __('¿Eliminar este beneficiario? Esta acción no se puede deshacer.') }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="{{ __('Eliminar') }}"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No se encontraron beneficiarios.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $beneficiarios->links('pagination::bootstrap-5') }}</div>
</x-layout>
