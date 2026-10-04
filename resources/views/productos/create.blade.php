@extends('layouts.app')
@section('titulo', 'Nuevo producto')
@section('contenido')
<h2 class="mb-3">Registrar producto</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('productos.store') }}">
        @csrf
        @include('productos._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
