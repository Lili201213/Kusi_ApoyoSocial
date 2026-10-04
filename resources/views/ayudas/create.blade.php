@extends('layouts.app')
@section('titulo', 'Registrar ayuda')

@section('contenido')
<h2 class="mb-3">Registrar ayuda entregada</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('ayudas.store') }}">
        @csrf
        @if ($seleccionado) <input type="hidden" name="desde_ficha" value="1"> @endif

        <div class="row g-3">
            <div class="col-12">@include('partials.selector_beneficiario')</div>

            <div class="col-md-6">
                <label class="form-label">Tipo de ayuda *</label>
                <select name="tipo_ayuda_id" class="form-select @error('tipo_ayuda_id') is-invalid @enderror" required>
                    <option value="">— Seleccionar —</option>
                    @foreach ($tipos as $t)
                        <option value="{{ $t->id }}" @selected(old('tipo_ayuda_id') == $t->id)>{{ $t->nombre }}</option>
                    @endforeach
                </select>
                @error('tipo_ayuda_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha de entrega *</label>
                <input type="date" name="fecha_entrega" value="{{ old('fecha_entrega', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                       class="form-control @error('fecha_entrega') is-invalid @enderror" required>
                @error('fecha_entrega') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Descripción de lo entregado</label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}" class="form-control @error('descripcion') is-invalid @enderror"
                       placeholder="Ej: 1 canasta básica, 2 frazadas...">
                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Observaciones</label>
                <textarea name="observacion" rows="3" class="form-control @error('observacion') is-invalid @enderror">{{ old('observacion') }}</textarea>
                @error('observacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            <a href="{{ $seleccionado ? route('beneficiarios.show', $seleccionado) : route('ayudas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
