@extends('layouts.app')
@section('titulo', $tipo === 'entrada' ? 'Registrar entrada' : 'Registrar salida')

@section('contenido')
@php $fmt = fn ($n) => \App\Services\InventarioService::fmt($n); @endphp
<h2 class="mb-3">{{ $tipo === 'entrada' ? 'Registrar entrada de producto' : 'Registrar salida de producto' }}</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route($tipo === 'entrada' ? 'movimientos.entrada.store' : 'movimientos.salida.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Producto *</label>
                <select name="producto_id" class="form-select @error('producto_id') is-invalid @enderror" required>
                    <option value="">— Seleccionar —</option>
                    @foreach ($productos as $p)
                        <option value="{{ $p->id }}" @selected(old('producto_id', $seleccionado) == $p->id)>
                            {{ $p->nombre }} (stock: {{ $fmt($p->stock_actual) }} {{ $p->unidad_medida }})
                        </option>
                    @endforeach
                </select>
                @error('producto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Cantidad *</label>
                <input type="number" step="0.01" min="0.01" name="cantidad" value="{{ old('cantidad') }}"
                       class="form-control @error('cantidad') is-invalid @enderror" required>
                @error('cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha *</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                       class="form-control @error('fecha') is-invalid @enderror" required>
                @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Motivo {{ $tipo === 'salida' ? '*' : '(opcional)' }}</label>
                <input type="text" name="motivo" value="{{ old('motivo') }}" class="form-control @error('motivo') is-invalid @enderror"
                       placeholder="{{ $tipo === 'salida' ? 'Ej: entrega a beneficiarios, producto vencido, ajuste de inventario...' : 'Ej: compra, ajuste de inventario...' }}"
                       {{ $tipo === 'salida' ? 'required' : '' }}>
                @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn {{ $tipo === 'entrada' ? 'btn-success' : 'btn-warning' }}"><i class="bi bi-check-lg me-1"></i>Registrar {{ $tipo }}</button>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
