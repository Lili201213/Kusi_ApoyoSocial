@extends('layouts.app')
@section('titulo', 'Nuevo usuario')
@section('contenido')
<h2 class="mb-3">Registrar usuario</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('usuarios.store') }}" autocomplete="off">
        @csrf
        @include('usuarios._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
