<x-layout>
    <x-slot:title>{{ __('Nuevo producto') }}</x-slot>

<h2 class="mb-3">{{ __('Registrar producto') }}</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('productos.store') }}">
        @csrf
        @include('productos._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>{{ __('Guardar') }}</button>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">{{ __('Cancelar') }}</a>
        </div>
    </form>
</div></div>
</x-layout>
