@extends('layouts.app')
@section('titulo', 'Editar donante')
@section('contenido')
<h2 class="mb-3">Editar donante</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('donantes.update', $donante) }}">
        @csrf @method('PUT')
        @include('donantes._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Actualizar</button>
            <a href="{{ route('donantes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
