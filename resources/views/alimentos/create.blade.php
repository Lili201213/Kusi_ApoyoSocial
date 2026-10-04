@extends('layouts.app')
@section('titulo', 'Registrar entrega de alimentos')

@section('contenido')
<h2 class="mb-3">Registrar entrega de alimentos / almuerzos</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('alimentos.store') }}">
        @csrf
        @if ($seleccionado) <input type="hidden" name="desde_ficha" value="1"> @endif

        <div class="row g-3">
            <div class="col-12">@include('partials.selector_beneficiario')</div>

            <div class="col-md-4">
                <label class="form-label">Fecha *</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                       class="form-control @error('fecha') is-invalid @enderror" required>
                @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipo de entrega *</label>
                <select name="tipo_entrega" class="form-select @error('tipo_entrega') is-invalid @enderror" required>
                    @foreach ($tipos as $t)
                        <option value="{{ $t }}" @selected(old('tipo_entrega', 'almuerzo') === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
                @error('tipo_entrega') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Raciones *</label>
                <input type="number" name="raciones" min="1" max="50" value="{{ old('raciones', 1) }}"
                       class="form-control @error('raciones') is-invalid @enderror" required>
                @error('raciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Observación</label>
                <input type="text" name="observacion" value="{{ old('observacion') }}" class="form-control">
            </div>
        </div>

        <div class="mt-4 d-flex flex-wrap gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            <button name="otro" value="1" class="btn btn-outline-success"><i class="bi bi-plus-circle me-1"></i>Guardar y registrar otro</button>
            <a href="{{ $seleccionado ? route('beneficiarios.show', $seleccionado) : route('alimentos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
