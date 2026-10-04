@extends('layouts.app')
@section('titulo', 'Registrar donación')

@section('contenido')
@php
    $fmt = fn ($n) => \App\Services\InventarioService::fmt($n);
    $items = old('items', [['producto_id' => '', 'cantidad' => '']]);
@endphp
<h2 class="mb-3">Registrar donación</h2>

@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ route('donaciones.store') }}">
    @csrf
    <div class="card shadow-sm mb-3"><div class="card-body"><div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Donante *</label>
            <select name="donante_id" class="form-select @error('donante_id') is-invalid @enderror" required>
                <option value="">— Seleccionar —</option>
                @foreach ($donantes as $d)
                    <option value="{{ $d->id }}" @selected(old('donante_id') == $d->id)>{{ $d->nombre_razon_social }}{{ $d->documento ? ' — '.$d->documento : '' }}</option>
                @endforeach
            </select>
            @if (auth()->user()->tienePermiso('donaciones.crear'))
                <div class="form-text"><a href="{{ route('donantes.create', ['volver' => 'donacion']) }}">¿No está en la lista? Registrar donante nuevo</a></div>
            @endif
        </div>
        <div class="col-md-3">
            <label class="form-label">Fecha *</label>
            <input type="date" name="fecha_donacion" value="{{ old('fecha_donacion', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Observación</label>
            <input type="text" name="observacion" value="{{ old('observacion') }}" class="form-control">
        </div>
    </div></div></div>

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-semibold">Productos donados</div>
        <div class="card-body">
            <div id="filas">
                @foreach ($items as $i => $it)
                    <div class="row g-2 mb-2 fila">
                        <div class="col-md-7">
                            <select name="items[{{ $i }}][producto_id]" class="form-select" required>
                                <option value="">— Producto —</option>
                                @foreach ($productos as $p)
                                    <option value="{{ $p->id }}" @selected(($it['producto_id'] ?? '') == $p->id)>{{ $p->nombre }} ({{ $p->unidad_medida }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-8 col-md-3">
                            <input type="number" step="0.01" min="0.01" name="items[{{ $i }}][cantidad]" value="{{ $it['cantidad'] ?? '' }}" class="form-control" placeholder="Cantidad" required>
                        </div>
                        <div class="col-4 col-md-2"><button type="button" class="btn btn-outline-danger w-100 quitar" title="Quitar"><i class="bi bi-trash"></i></button></div>
                    </div>
                @endforeach
            </div>
            <button type="button" id="agregar" class="btn btn-outline-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Agregar otro producto</button>
            @if ($productos->isEmpty())
                <div class="text-danger small mt-2">No hay productos activos. Primero registra los productos en Inventario.</div>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Guardar donación</button>
        <a href="{{ route('donaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    (function () {
        const filas = document.getElementById('filas');
        let n = filas.querySelectorAll('.fila').length + 100; // índice único para filas nuevas

        document.getElementById('agregar').addEventListener('click', function () {
            const copia = filas.querySelector('.fila').cloneNode(true);
            copia.querySelectorAll('select, input').forEach(function (el) {
                el.name = el.name.replace(/items\[\d+\]/, 'items[' + n + ']');
                if (el.tagName === 'SELECT') { el.selectedIndex = 0; } else { el.value = ''; }
            });
            n++;
            filas.appendChild(copia);
        });

        filas.addEventListener('click', function (e) {
            const btn = e.target.closest('.quitar');
            if (!btn) return;
            if (filas.querySelectorAll('.fila').length > 1) btn.closest('.fila').remove();
        });
    })();
</script>
@endpush
