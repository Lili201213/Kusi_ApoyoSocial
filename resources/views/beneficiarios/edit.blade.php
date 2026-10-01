@extends('layouts.app')
@section('titulo', 'Editar beneficiario')

@section('contenido')
<h2 class="mb-3">Editar beneficiario</h2>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('beneficiarios.update', $beneficiario) }}">
        @csrf
        @method('PUT')
        @include('beneficiarios._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Actualizar</button>
            <a href="{{ route('beneficiarios.show', $beneficiario) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
