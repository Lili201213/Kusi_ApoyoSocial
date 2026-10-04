@php $campo = fn ($n, $d = '') => old($n, $donante->{$n} ?? $d); @endphp
@if (! empty($volver)) <input type="hidden" name="volver" value="{{ $volver }}"> @endif
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Tipo de persona *</label>
        <select name="tipo_persona" class="form-select">
            <option value="natural" @selected($campo('tipo_persona', 'natural') === 'natural')>Natural</option>
            <option value="juridica" @selected($campo('tipo_persona') === 'juridica')>Jurídica (empresa)</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Nombre o razón social *</label>
        <input type="text" name="nombre_razon_social" value="{{ $campo('nombre_razon_social') }}"
               class="form-control @error('nombre_razon_social') is-invalid @enderror" required>
        @error('nombre_razon_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">DNI / RUC</label>
        <input type="text" name="documento" value="{{ $campo('documento') }}" class="form-control @error('documento') is-invalid @enderror">
        @error('documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" value="{{ $campo('telefono') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Correo</label>
        <input type="email" name="email" value="{{ $campo('email') }}" class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" value="{{ $campo('direccion') }}" class="form-control">
    </div>
</div>
