@extends('layouts.app')
@section('titulo', 'Nuevo donante')
@section('contenido')
<h2 class="mb-3">Registrar donante</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('donantes.store') }}">
        @csrf
        @include('donantes._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            <a href="{{ route('donantes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
