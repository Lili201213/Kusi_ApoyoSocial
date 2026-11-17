<x-layout>
    <x-slot:title>{{ __('Donación #:id', ['id' => $donacion->id]) }}</x-slot>

@php $u = auth()->user(); $fmt = fn ($n) => \App\Services\InventarioService::fmt($n); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h2 class="mb-0">{{ __('Donación #:id', ['id' => $donacion->id]) }}</h2>
        <span class="badge {{ $donacion->estaAnulada() ? 'text-bg-secondary' : 'text-bg-success' }}">{{ $donacion->estaAnulada() ? __('Anulada') : __('Registrada') }}</span>
    </div>
    <a href="{{ route('donaciones.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>{{ __('Volver') }}</a>
</div>

<div class="card shadow-sm mb-4"><div class="card-body"><div class="row g-3">
    <div class="col-md-4"><div class="text-muted small">{{ __('Donante') }}</div>{{ $donacion->donante->nombre_razon_social }}
        @if ($donacion->donante->documento) <div class="text-muted small">{{ $donacion->donante->documento }}</div> @endif</div>
    <div class="col-md-3"><div class="text-muted small">{{ __('Fecha') }}</div>{{ $donacion->fecha_donacion->format('d/m/Y') }}</div>
    <div class="col-md-3"><div class="text-muted small">{{ __('Registró') }}</div>{{ $donacion->usuario->name }}</div>
    <div class="col-12"><div class="text-muted small">{{ __('Observación') }}</div>{{ $donacion->observacion ?: '—' }}</div>
    @if ($donacion->estaAnulada())
        <div class="col-12"><div class="alert alert-secondary mb-0">
            <strong>{{ __('Anulada') }}</strong> {{ __('el :fecha por :usuario. Motivo: :motivo', ['fecha' => $donacion->anulada_at?->format('d/m/Y H:i'), 'usuario' => $donacion->anuladaPor->name ?? '—', 'motivo' => $donacion->motivo_anulacion]) }}</div></div>
    @endif
</div></div></div>

<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">{{ __('Productos recibidos') }}</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>{{ __('Producto') }}</th><th class="text-end">{{ __('Cantidad') }}</th></tr></thead>
            <tbody>
            @foreach ($donacion->detalles as $det)
                <tr>
                    <td><a href="{{ route('productos.show', $det->producto_id) }}" class="text-decoration-none">{{ $det->producto->nombre }}</a></td>
                    <td class="text-end">{{ $fmt($det->cantidad) }} {{ $det->producto->unidad_medida }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if (! $donacion->estaAnulada() && $u->tienePermiso('donaciones.anular'))
<div class="card border-danger shadow-sm">
    <div class="card-header text-danger fw-semibold">{{ __('Anular donación') }}</div>
    <div class="card-body">
        <p class="small text-muted mb-2">{{ __('Las donaciones no se borran. Al anularla, el sistema descuenta del inventario lo que ingresó. Si parte de lo donado ya salió, no se podrá anular.') }}</p>
        <form method="POST" action="{{ route('donaciones.anular', $donacion) }}" class="row g-2"
              onsubmit="return confirm(@js(__('¿Anular esta donación y revertir el stock?')))">
            @csrf
            <div class="col-md-9">
                <input type="text" name="motivo_anulacion" value="{{ old('motivo_anulacion') }}" placeholder="{{ __('Motivo de la anulación') }}"
                       class="form-control @error('motivo_anulacion') is-invalid @enderror" required>
                @error('motivo_anulacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3"><button class="btn btn-danger w-100">{{ __('Anular donación') }}</button></div>
        </form>
    </div>
</div>
@endif
</x-layout>
