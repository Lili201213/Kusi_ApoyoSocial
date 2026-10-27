@extends('layouts.app')
@section('titulo', 'Mi perfil')

@section('contenido')
@php $u = auth()->user(); @endphp
<h2 class="mb-3">Mi perfil</h2>

<div class="row g-4">
    {{-- Columna izquierda: identidad + foto + cambiar contraseña --}}
    <div class="col-lg-4">
        <div class="card shadow-sm text-center">
            <div class="card-body p-4">
                <div class="perfil-avatar mx-auto mb-3">
                    @if ($u->foto_url)
                        <img src="{{ $u->foto_url }}" alt="Foto de {{ $u->name }}">
                    @else
                        <span>{{ $u->inicial }}</span>
                    @endif
                </div>
                <h4 class="mb-0">{{ $u->name }}</h4>
                <div class="text-muted">{{ $u->rol->nombre ?? 'Sin rol' }}</div>
                <div class="mt-2"><i class="bi bi-person-badge me-1 text-muted"></i><code>{{ $u->usuario }}</code></div>
                @if ($u->email)
                    <div class="small text-muted mt-1"><i class="bi bi-envelope me-1"></i>{{ $u->email }}</div>
                    @if ($u->hasVerifiedEmail())
                        <span class="badge bg-success-subtle text-success-emphasis mt-2">
                            <i class="bi bi-patch-check-fill me-1"></i>Correo verificado
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis mt-2">
                            <i class="bi bi-exclamation-circle me-1"></i>Correo sin verificar
                        </span>
                    @endif
                @endif
                <hr>

                <form method="POST" action="{{ route('perfil.foto') }}" enctype="multipart/form-data" class="text-start">
                    @csrf
                    <label class="form-label">Foto de perfil</label>
                    <input type="file" name="foto" accept="image/png,image/jpeg,image/webp"
                           class="form-control mb-1 @error('foto') is-invalid @enderror" required>
                    @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text mb-2">JPG, PNG o WEBP · máximo 2 MB</div>
                    <button class="btn btn-success btn-sm w-100"><i class="bi bi-camera me-1"></i>Guardar foto</button>
                </form>

                @if ($u->foto)
                    <form method="POST" action="{{ route('perfil.foto.borrar') }}" class="mt-2" onsubmit="return confirm('¿Quitar tu foto de perfil?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-trash me-1"></i>Quitar foto</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="card shadow-sm mt-4">
            <div class="card-header"><i class="bi bi-key me-1"></i>Cambiar contraseña</div>
            <div class="card-body">
                <form method="POST" action="{{ route('perfil.clave') }}" autocomplete="off">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Contraseña actual</label>
                        <input type="password" name="password_actual" autocomplete="current-password"
                               class="form-control @error('password_actual') is-invalid @enderror" required>
                        @error('password_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" name="password" autocomplete="new-password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        <div class="form-text">Mínimo 8 caracteres, con letras y números.</div>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="form-control" required>
                    </div>
                    <button class="btn btn-success mt-3 w-100"><i class="bi bi-key me-1"></i>Cambiar contraseña</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Columna derecha: datos personales + eliminar cuenta --}}
    <div class="col-lg-8">
        {{-- Datos personales --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><i class="bi bi-person-lines-fill me-1"></i>Datos personales</div>
            <div class="card-body">
                <form method="POST" action="{{ route('perfil.datos') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre completo *</label>
                            <input type="text" name="name" value="{{ old('name', $u->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $u->telefono) }}" class="form-control @error('telefono') is-invalid @enderror">
                            @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Correo electrónico *</label>
                            <input type="email" name="email" value="{{ old('email', $u->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                            <div class="form-text">
                                Aquí recibirás el enlace si olvidas tu contraseña. Usa un correo al que tengas acceso.
                                Si lo cambias, te enviaremos un enlace para verificar el nuevo correo y deberás confirmarlo para seguir usando el sistema.
                            </div>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usuario</label>
                            <input type="text" value="{{ $u->usuario }}" class="form-control" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <input type="text" value="{{ $u->rol->nombre ?? '—' }}" class="form-control" disabled>
                        </div>
                        <div class="col-12 form-text mt-0">El usuario y el rol solo los puede cambiar el administrador.</div>
                    </div>
                    <button class="btn btn-success mt-3"><i class="bi bi-check-lg me-1"></i>Guardar cambios</button>
                </form>
            </div>
        </div>

        {{-- Eliminar cuenta (no aparece para el administrador) --}}
        @if ($u->puedeEliminarCuenta())
        <div class="card shadow-sm border-danger">
            <div class="card-header text-danger"><i class="bi bi-exclamation-octagon me-1"></i>Eliminar cuenta</div>
            <div class="card-body">
                <p class="mb-3">
                    Al eliminar tu cuenta ya no podrás ingresar al sistema. Las ayudas, donaciones y
                    movimientos que registraste se conservan en el historial de la sociedad.
                    <strong>Esta acción no se puede deshacer por tu cuenta.</strong>
                </p>
                <form method="POST" action="{{ route('perfil.eliminar') }}"
                      onsubmit="return confirm('¿Seguro que quieres eliminar tu cuenta? Esta acción cerrará tu sesión.')">
                    @csrf @method('DELETE')
                    <label class="form-label">Escribe tu contraseña para confirmar</label>
                    <input type="password" name="password_eliminar" autocomplete="current-password"
                           class="form-control mb-2 @error('password_eliminar') is-invalid @enderror" required>
                    @error('password_eliminar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <button class="btn btn-danger mt-2"><i class="bi bi-trash me-1"></i>Eliminar mi cuenta</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
