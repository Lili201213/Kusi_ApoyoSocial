@extends('layouts.app')
@section('titulo', 'Usuarios')

@section('contenido')
@php $yo = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">Usuarios</h2>
    <a href="{{ route('usuarios.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Nuevo usuario</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4"><input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Nombre, usuario o correo"></div>
    <div class="col-6 col-md-3">
        <select name="rol_id" class="form-select">
            <option value="">Todos los roles</option>
            @foreach ($roles as $r)<option value="{{ $r->id }}" @selected(request('rol_id') == $r->id)>{{ $r->nombre }}</option>@endforeach
        </select>
    </div>
    <div class="col-6 col-md-2">
        <select name="estado" class="form-select">
            <option value="">Todos</option>
            <option value="1" @selected(request('estado') === '1')>Activos</option>
            <option value="0" @selected(request('estado') === '0')>Inactivos</option>
        </select>
    </div>
    <div class="col-12 col-md-3 d-flex gap-1">
        <button class="btn btn-outline-success flex-fill"><i class="bi bi-search me-1"></i>Buscar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            @forelse ($usuarios as $u)
                <tr>
                    <td class="fw-semibold">{{ $u->name }} @if ($u->id === $yo->id)<span class="badge text-bg-light border">tú</span>@endif</td>
                    <td><code>{{ $u->usuario }}</code></td>
                    <td>{{ $u->email ?: '—' }}</td>
                    <td><span class="badge {{ $u->rol_id === 1 ? 'text-bg-dark' : ($u->rol_id === 2 ? 'text-bg-success' : 'text-bg-info') }}">{{ $u->rol->nombre ?? 'Sin rol' }}</span></td>
                    <td><span class="badge {{ $u->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $u->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                        @if ($u->id !== $yo->id)
                            <form method="POST" action="{{ route('usuarios.estado', $u) }}" class="d-inline"
                                  onsubmit="return confirm('{{ $u->activo ? '¿Desactivar este usuario? Ya no podrá ingresar.' : '¿Activar este usuario?' }}')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $u->activo ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="{{ $u->activo ? 'Desactivar' : 'Activar' }}">
                                    <i class="bi {{ $u->activo ? 'bi-person-dash' : 'bi-person-check' }}"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No se encontraron usuarios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $usuarios->links('pagination::bootstrap-5') }}</div>
@endsection
