@extends('layouts.app')
@section('titulo', 'Reportes')

@section('contenido')
@php
    $reportes = [
        ['beneficiarios', 'bi-people', 'Beneficiarios', 'Listado de beneficiarios con su estado, edad y datos de contacto.'],
        ['ayudas', 'bi-box2-heart', 'Ayudas entregadas', 'Ayudas entregadas por fecha, tipo y beneficiario.'],
        ['alimentos', 'bi-egg-fried', 'Alimentos y almuerzos', 'Entregas de alimentos con el total de raciones.'],
        ['donaciones', 'bi-gift', 'Donaciones', 'Donaciones recibidas, con los productos de cada una.'],
        ['inventario', 'bi-boxes', 'Inventario', 'Existencias actuales y productos con stock bajo.'],
    ];
@endphp
<h2 class="mb-1">Reportes</h2>
<p class="text-muted">Elige un reporte, filtra lo que necesites y luego imprímelo (o guárdalo como PDF) o expórtalo a Excel.</p>

<div class="row g-3">
    @foreach ($reportes as [$ruta, $icono, $titulo, $desc])
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('reportes.ver', $ruta) }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="fs-2 text-success"><i class="bi {{ $icono }}"></i></div>
                        <h5 class="card-title text-dark mt-2">{{ $titulo }}</h5>
                        <p class="card-text text-muted mb-0">{{ $desc }}</p>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
