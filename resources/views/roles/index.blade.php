@extends('layouts.app')
@section('titulo', 'Roles y permisos')

@section('contenido')
<h2 class="mb-1">Roles y permisos</h2>
<p class="text-muted">Marca qué puede hacer cada rol. Los cambios se aplican de inmediato a todos los usuarios de ese rol.</p>

<div class="alert alert-info py-2 small">
    <i class="bi bi-info-circle me-1"></i>
    El <strong>Administrador</strong> siempre tiene todos los permisos. Los permisos con <i class="bi bi-lock-fill"></i>
    (gestionar usuarios y roles, eliminar y anular) son exclusivos del Administrador.
</div>

<form method="POST" action="{{ route('roles.update') }}">
    @csrf @method('PUT')
    <div class="card shadow-sm mb-3">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:280px">Funcionalidad</th>
                        @foreach ($roles as $r)<th class="text-center">{{ $r->nombre }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach ($grupos as $grupo => $permisos)
                    <tr class="table-secondary"><td colspan="{{ $roles->count() + 1 }}" class="fw-semibold">{{ $grupo }}</td></tr>
                    @foreach ($permisos as $p)
                        @php $bloqueado = \App\Http\Controllers\RolController::soloAdmin($p->nombre); @endphp
                        <tr>
                            <td>
                                {{ $p->descripcion }}
                                @if ($bloqueado) <i class="bi bi-lock-fill text-muted ms-1" title="Solo Administrador"></i> @endif
                                <div class="text-muted small"><code>{{ $p->nombre }}</code></div>
                            </td>
                            @foreach ($roles as $r)
                                @php $esAdmin = $r->id === 1; @endphp
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input"
                                           @if (! $esAdmin && ! $bloqueado) name="permisos[{{ $r->id }}][]" value="{{ $p->id }}" @endif
                                           @checked($esAdmin || $r->permisos->contains('id', $p->id))
                                           @disabled($esAdmin || $bloqueado)>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Guardar permisos</button>
</form>
@endsection
