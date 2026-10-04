@extends('layouts.app')
@section('titulo', 'Mi cuenta')

@section('contenido')
@php $u = auth()->user(); @endphp
<h2 class="mb-3">Mi cuenta</h2>
<div class="row g-3">
    <div class="col-lg-5"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Mis datos</div>
        <div class="card-body">
            <div class="text-muted small">Nombre</div><div class="mb-2">{{ $u->name }}</div>
            <div class="text-muted small">Usuario</div><div class="mb-2"><code>{{ $u->usuario }}</code></div>
            <div class="text-muted small">Correo</div><div class="mb-2">{{ $u->email ?: '—' }}</div>
            <div class="text-muted small">Rol</div><div>{{ $u->rol->nombre ?? '—' }}</div>
        </div></div></div>

    <div class="col-lg-7"><div class="card shadow-sm"><div class="card-header fw-semibold">Cambiar contraseña</div>
        <div class="card-body">
            <form method="POST" action="{{ route('cuenta.update') }}" autocomplete="off">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Contraseña actual</label>
                    <input type="password" name="password_actual" autocomplete="current-password" class="form-control @error('password_actual') is-invalid @enderror" required>
                    @error('password_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" name="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" required>
                    <div class="form-text">Mínimo 8 caracteres, con letras y números.</div>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" class="form-control" required>
                </div>
                <button class="btn btn-success"><i class="bi bi-key me-1"></i>Cambiar contraseña</button>
            </form>
        </div></div></div>
</div>
@endsection
