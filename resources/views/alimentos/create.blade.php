<x-layout>
    <x-slot:title>{{ __('Registrar entrega de alimentos') }}</x-slot>

<h2 class="mb-3">{{ __('Registrar entrega de alimentos / almuerzos') }}</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('alimentos.store') }}">
        @csrf
        @if ($seleccionado) <input type="hidden" name="desde_ficha" value="1"> @endif

        <div class="row g-3">
            <div class="col-12">@include('partials.selector_beneficiario')</div>

            <div class="col-md-4">
                <label class="form-label">{{ __('Fecha') }} *</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                       class="form-control @error('fecha') is-invalid @enderror" required>
                @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('Tipo de entrega') }} *</label>
                <select name="tipo_entrega" class="form-select @error('tipo_entrega') is-invalid @enderror" required>
                    @foreach ($tipos as $t)
                        <option value="{{ $t }}" @selected(old('tipo_entrega', 'almuerzo') === $t)>{{ __($t === 'almuerzo' ? 'Almuerzo' : ($t === 'desayuno' ? 'Desayuno' : ($t === 'cena' ? 'Cena' : ($t === 'canasta' ? 'Canasta' : ucfirst($t))))) }}</option>
                    @endforeach
                </select>
                @error('tipo_entrega') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('Raciones') }} *</label>
                <input type="number" name="raciones" min="1" max="50" value="{{ old('raciones', 1) }}"
                       class="form-control @error('raciones') is-invalid @enderror" required>
                @error('raciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('Observación') }}</label>
                <input type="text" name="observacion" value="{{ old('observacion') }}" class="form-control">
            </div>
        </div>

        <div class="mt-4 d-flex flex-wrap gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>{{ __('Guardar') }}</button>
            <button name="otro" value="1" class="btn btn-outline-success"><i class="bi bi-plus-circle me-1"></i>{{ __('Guardar y registrar otro') }}</button>
            <a href="{{ $seleccionado ? route('beneficiarios.show', $seleccionado) : route('alimentos.index') }}" class="btn btn-outline-secondary">{{ __('Cancelar') }}</a>
        </div>
    </form>
</div></div>
</x-layout>
