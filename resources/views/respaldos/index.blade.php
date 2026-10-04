@extends('layouts.app')
@section('titulo', 'Copias de seguridad')

@section('contenido')
<h2 class="mb-1">Copias de seguridad</h2>
<p class="text-muted">Descarga una copia completa de la información del sistema (base de datos <code>{{ $baseDatos }}</code>).</p>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h5 class="card-title"><i class="bi bi-database-down me-1 text-success"></i>Generar copia ahora</h5>
            <p class="card-text">Se descargará un archivo <strong>.sql</strong> con beneficiarios, ayudas, donaciones, inventario, usuarios y permisos.</p>
            <form method="POST" action="{{ route('respaldos.descargar') }}">
                @csrf
                <button class="btn btn-success btn-lg"><i class="bi bi-download me-2"></i>Descargar copia de seguridad</button>
            </form>
            <div class="alert alert-warning mt-3 mb-0 small">
                <i class="bi bi-shield-exclamation me-1"></i>
                El archivo contiene información confidencial de los beneficiarios. Guárdalo en un lugar seguro
                (USB o disco externo) y no lo compartas por canales públicos.
            </div>
        </div></div>

        <div class="card shadow-sm"><div class="card-body">
            <h5 class="card-title"><i class="bi bi-arrow-counterclockwise me-1 text-primary"></i>¿Cómo restaurar una copia?</h5>
            <ol class="mb-0">
                <li>Abre <strong>phpMyAdmin</strong> (con MySQL encendido en XAMPP).</li>
                <li>Entra a la base de datos <code>{{ $baseDatos }}</code>.</li>
                <li>Pestaña <strong>Importar</strong> → elige el archivo .sql → <strong>Importar</strong>.</li>
            </ol>
            <p class="small text-muted mt-2 mb-0">La copia reemplaza las tablas actuales por las del archivo. Haz una copia nueva antes de restaurar.</p>
        </div></div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm"><div class="card-header fw-semibold">Qué incluye la copia</div>
            <ul class="list-group list-group-flush">
                @foreach ($tablas as $t)
                    <li class="list-group-item d-flex justify-content-between small">
                        <code>{{ $t['nombre'] }}</code><span class="badge text-bg-light border">{{ $t['filas'] }} registro(s)</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
