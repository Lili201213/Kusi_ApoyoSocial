<x-layout>
    <x-slot:title>{{ __('Editar producto') }}</x-slot>

<h2 class="mb-1">{{ __('Editar producto') }}</h2>
<p class="text-muted">{{ __('El stock no se edita aquí: cambia solo con entradas y salidas.') }}</p>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('productos.update', $producto) }}">
        @csrf @method('PUT')
        @include('productos._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>{{ __('Actualizar') }}</button>
            <a href="{{ route('productos.show', $producto) }}" class="btn btn-outline-secondary">{{ __('Cancelar') }}</a>
        </div>
    </form>
</div></div>
</x-layout>
