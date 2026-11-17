@php
    $campo = fn ($n) => old($n, $beneficiario->{$n} ?? '');
    $fnac = old('fecha_nacimiento', optional($beneficiario->fecha_nacimiento)->format('Y-m-d'));
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('DNI') }} *</label>
        <input type="text" name="dni" value="{{ $campo('dni') }}" maxlength="8" inputmode="numeric"
               class="form-control @error('dni') is-invalid @enderror" required>
        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('Nombres') }} *</label>
        <input type="text" name="nombres" value="{{ $campo('nombres') }}" class="form-control @error('nombres') is-invalid @enderror" required>
        @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('Apellidos') }} *</label>
        <input type="text" name="apellidos" value="{{ $campo('apellidos') }}" class="form-control @error('apellidos') is-invalid @enderror" required>
        @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('Fecha de nacimiento') }}</label>
        <input type="date" name="fecha_nacimiento" value="{{ $fnac }}" max="{{ date('Y-m-d') }}"
               class="form-control @error('fecha_nacimiento') is-invalid @enderror">
        @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('Sexo') }}</label>
        <select name="sexo" class="form-select @error('sexo') is-invalid @enderror">
            <option value="">— {{ __('Seleccionar') }} —</option>
            @foreach (['M' => __('Masculino'), 'F' => __('Femenino'), 'Otro' => __('Otro')] as $k => $v)
                <option value="{{ $k }}" @selected($campo('sexo') === $k)>{{ $v }}</option>
            @endforeach
        </select>
        @error('sexo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('Teléfono') }}</label>
        <input type="text" name="telefono" value="{{ $campo('telefono') }}" class="form-control @error('telefono') is-invalid @enderror">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">{{ __('Dirección') }}</label>
        <input type="text" name="direccion" value="{{ $campo('direccion') }}" class="form-control @error('direccion') is-invalid @enderror">
        @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('N.º de familiares en el hogar') }} *</label>
        <input type="number" name="num_familiares" min="1" max="30" value="{{ $campo('num_familiares') ?: 1 }}"
               class="form-control @error('num_familiares') is-invalid @enderror" required>
        @error('num_familiares') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">{{ __('Situación socioeconómica') }}</label>
        <input type="text" name="situacion" value="{{ $campo('situacion') }}" class="form-control @error('situacion') is-invalid @enderror"
               placeholder="{{ __('Ej: adulto mayor sin ingresos, madre soltera, desempleo...') }}">
        @error('situacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('Observaciones') }}</label>
        <textarea name="observaciones" rows="3" class="form-control @error('observaciones') is-invalid @enderror">{{ $campo('observaciones') }}</textarea>
        @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @if ($beneficiario->exists)
        <div class="col-md-4">
            <label class="form-label">{{ __('Estado') }}</label>
            <select name="estado" class="form-select">
                <option value="activo" @selected($campo('estado') === 'activo')>{{ __('Activo') }}</option>
                <option value="inactivo" @selected($campo('estado') === 'inactivo')>{{ __('Inactivo') }}</option>
            </select>
        </div>
    @endif
</div>
