@extends('layouts.app')
@section('titulo', 'Tipos de ayuda')

@section('contenido')
<h2 class="mb-3">Tipos de ayuda</h2>

<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">Registrar nuevo tipo</div>
    <div class="card-body">
        <form method="POST" action="{{ route('tipos_ayuda.store') }}" class="row g-2">
            @csrf
            <div class="col-md-4">
                <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre (ej: Pañales)"
                       class="form-control @error('nombre') is-invalid @enderror" required>
                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <input type="text" name="descripcion" value="{{ old('descripcion') }}" placeholder="Descripción (opcional)" class="form-control">
            </div>
            <div class="col-md-2"><button class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i>Agregar</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Nombre</th><th>Descripción</th><th class="text-center">Ayudas entregadas</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @foreach ($tipos as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->nombre }}</td>
                    <td>{{ $t->descripcion ?: '—' }}</td>
                    <td class="text-center">{{ $t->ayudas_count }}</td>
                    <td><span class="badge {{ $t->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $t->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="text-end text-nowrap">
                        <form method="POST" action="{{ route('tipos_ayuda.estado', $t) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $t->activo ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                {{ $t->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        @if (auth()->user()->tienePermiso('tipos_ayuda.eliminar'))
                            <form method="POST" action="{{ route('tipos_ayuda.destroy', $t) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este tipo de ayuda? Solo es posible si nunca se ha usado.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
