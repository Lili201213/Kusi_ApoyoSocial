@php $campo = fn ($n, $d = '') => old($n, $producto->{$n} ?? $d); @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" value="{{ $campo('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Categoría</label>
        <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror">
            <option value="">— Sin categoría —</option>
            @foreach ($categorias as $c)
                <option value="{{ $c->id }}" @selected($campo('categoria_id') == $c->id)>{{ $c->nombre }}</option>
            @endforeach
        </select>
        @error('categoria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Unidad de medida *</label>
        <select name="unidad_medida" class="form-select">
            @foreach ($unidades as $un)
                <option value="{{ $un }}" @selected($campo('unidad_medida', 'unidad') === $un)>{{ $un }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Descripción</label>
        <input type="text" name="descripcion" value="{{ $campo('descripcion') }}" class="form-control @error('descripcion') is-invalid @enderror">
        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Stock mínimo *</label>
        <input type="number" step="0.01" min="0" name="stock_minimo" value="{{ $campo('stock_minimo', 0) }}"
               class="form-control @error('stock_minimo') is-invalid @enderror" required>
        <div class="form-text">Avisa cuando el stock llegue a este nivel.</div>
        @error('stock_minimo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    @unless ($producto->exists)
        <div class="col-md-3">
            <label class="form-label">Stock inicial</label>
            <input type="number" step="0.01" min="0" name="stock_inicial" value="{{ old('stock_inicial', 0) }}"
                   class="form-control @error('stock_inicial') is-invalid @enderror">
            <div class="form-text">Se registra como una entrada.</div>
            @error('stock_inicial') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    @endunless
</div>
