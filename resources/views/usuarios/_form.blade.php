@php $campo = fn ($n, $d = '') => old($n, $usuario->{$n} ?? $d); @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('Nombre completo') }} *</label>
        <input type="text" name="name" value="{{ $campo('name') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Usuario') }} *</label>
        <input type="text" name="usuario" value="{{ $campo('usuario') }}" autocomplete="off"
               class="form-control @error('usuario') is-invalid @enderror" required>
        <div class="form-text">{{ __('Con este usuario inicia sesión. Sin espacios.') }}</div>
        @error('usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Correo electrónico') }} *</label>
        <input type="email" name="email" value="{{ $campo('email') }}" class="form-control @error('email') is-invalid @enderror" required>
        <div class="form-text">{{ __('Se usa para recuperar la contraseña.') }}</div>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Rol') }} *</label>
        <select name="rol_id" class="form-select @error('rol_id') is-invalid @enderror" {{ $esPropio ? 'disabled' : 'required' }}>
            <option value="">— {{ __('Seleccionar') }} —</option>
            @foreach ($roles as $r)
                <option value="{{ $r->id }}" @selected($campo('rol_id') == $r->id)>{{ $r->nombre }}</option>
            @endforeach
        </select>
        @if ($esPropio) <div class="form-text">{{ __('No puedes cambiar tu propio rol.') }}</div> @endif
        @error('rol_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ $usuario->exists ? __('Nueva contraseña') : __('Contraseña').' *' }}</label>
        <input type="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror" {{ $usuario->exists ? '' : 'required' }}>
        <div class="form-text">{{ $usuario->exists ? __('Déjala vacía para no cambiarla.').' ' : '' }}{{ __('Mínimo 8 caracteres, con letras y números.') }}</div>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Confirmar contraseña') }}</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" class="form-control">
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1"
                   @checked(old('activo', $usuario->activo ?? true)) {{ $esPropio ? 'disabled' : '' }}>
            <label class="form-check-label" for="activo">{{ __('Usuario activo (puede iniciar sesión)') }}</label>
        </div>
        @if ($esPropio) <div class="form-text">{{ __('No puedes desactivarte a ti mismo.') }}</div> @endif
    </div>
</div>
