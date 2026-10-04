@extends('layouts.app')
@section('titulo', 'Ayudas entregadas')

@section('contenido')
@php $u = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">Ayudas entregadas</h2>
    @if ($u->tienePermiso('ayudas.crear'))
        <a href="{{ route('ayudas.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Registrar ayuda</a>
    @endif
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Beneficiario (DNI o nombre)">
    </div>
    <div class="col-12 col-md-3">
        <select name="tipo_ayuda_id" class="form-select">
            <option value="">Todos los tipos</option>
            @foreach ($tipos as $t)
                <option value="{{ $t->id }}" @selected(request('tipo_ayuda_id') == $t->id)>{{ $t->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2"><input type="date" name="desde" value="{{ request('desde') }}" class="form-control" title="Desde"></div>
    <div class="col-6 col-md-2"><input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" title="Hasta"></div>
    <div class="col-12 col-md-1 d-flex gap-1">
        <button class="btn btn-outline-success flex-fill" title="Buscar"><i class="bi bi-search"></i></button>
        <a href="{{ route('ayudas.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Fecha</th><th>Beneficiario</th><th>Tipo de ayuda</th><th>Descripción</th><th>Registró</th>@if ($u->tienePermiso('ayudas.eliminar'))<th></th>@endif</tr>
            </thead>
            <tbody>
            @forelse ($ayudas as $a)
                <tr>
                    <td class="text-nowrap">{{ $a->fecha_entrega->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('beneficiarios.show', $a->beneficiario) }}" class="text-decoration-none">
                            {{ $a->beneficiario->apellidos }}, {{ $a->beneficiario->nombres }}
                        </a>
                        <div class="text-muted small">{{ $a->beneficiario->dni }}</div>
                    </td>
                    <td><span class="badge text-bg-success">{{ $a->tipoAyuda->nombre }}</span></td>
                    <td>{{ $a->descripcion ?: '—' }}</td>
                    <td>{{ $a->usuario->name }}</td>
                    @if ($u->tienePermiso('ayudas.eliminar'))
                    <td class="text-end">
                        @if ($u->tienePermiso('ayudas.eliminar'))
                            <form method="POST" action="{{ route('ayudas.destroy', $a) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este registro de ayuda?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay ayudas registradas con esos filtros.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $ayudas->links('pagination::bootstrap-5') }}</div>
@endsection
