@extends('layouts.app')
@section('titulo', 'Inicio')

@section('contenido')
    <h2 class="mb-1">Bienvenido, {{ auth()->user()->name }}</h2>
    <p class="text-muted">Rol: {{ auth()->user()->rol->nombre }}</p>

    <div class="row g-3">
        <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
            <div class="text-muted small">Beneficiarios</div><div class="fs-3 fw-bold">{{ \App\Models\Beneficiario::count() }}</div>
        </div></div></div>
        <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
            <div class="text-muted small">Ayudas entregadas</div><div class="fs-3 fw-bold">{{ \App\Models\AyudaEntregada::count() }}</div>
        </div></div></div>
        <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
            <div class="text-muted small">Donaciones</div><div class="fs-3 fw-bold">{{ \App\Models\Donacion::count() }}</div>
        </div></div></div>
        <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
            <div class="text-muted small">Productos</div><div class="fs-3 fw-bold">{{ \App\Models\Producto::count() }}</div>
        </div></div></div>
    </div>
@endsection
