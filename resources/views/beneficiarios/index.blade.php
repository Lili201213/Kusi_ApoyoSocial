@extends('layouts.app')
@section('titulo', 'Beneficiarios')

@section('contenido')
@php $u = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">Beneficiarios</h2>
    @if ($u->tienePermiso('beneficiarios.crear'))
        <a href="{{ route('beneficiarios.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i>Nuevo beneficiario
        </a>
    @endif
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-6">
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por DNI, nombres o apellidos">
    </div>
    <div class="col-6 col-md-3">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <option value="activo" @selected($estado === 'activo')>Activos</option>
            <option value="inactivo" @selected($estado === 'inactivo')>Inactivos</option>
        </select>
    </div>
    <div class="col-6 col-md-3 d-flex gap-2">
        <button class="btn btn-outline-success flex-fill"><i class="bi bi-search me-1"></i>Buscar</button>
        <a href="{{ route('beneficiarios.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>DNI</th><th>Nombre completo</th><th>Teléfono</th>
                    <th class="text-center">Familiares</th><th>Estado</th><th class="text-end">Acciones</th>
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
                        <span class="badge {{ $b->estado === 'activo' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($b->estado) }}</span>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('beneficiarios.show', $b) }}" class="btn btn-sm btn-outline-primary" title="Ver ficha"><i class="bi bi-eye"></i></a>
                        @if ($u->tienePermiso('beneficiarios.editar'))
                            <a href="{{ route('beneficiarios.edit', $b) }}" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No se encontraron beneficiarios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $beneficiarios->links('pagination::bootstrap-5') }}</div>
@endsection
