@extends('layouts.app')
@section('titulo', $beneficiario->nombres.' '.$beneficiario->apellidos)

@section('contenido')
@php $u = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h2 class="mb-0">{{ $beneficiario->nombres }} {{ $beneficiario->apellidos }}</h2>
        <span class="badge {{ $beneficiario->estado === 'activo' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($beneficiario->estado) }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('beneficiarios.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
        @if ($u->tienePermiso('beneficiarios.editar'))
            <a href="{{ route('beneficiarios.edit', $beneficiario) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>
        @endif
    </div>
</div>

{{-- Datos personales --}}
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">Datos personales</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><div class="text-muted small">DNI</div>{{ $beneficiario->dni }}</div>
            <div class="col-md-3"><div class="text-muted small">Fecha de nacimiento</div>
                {{ $beneficiario->fecha_nacimiento ? $beneficiario->fecha_nacimiento->format('d/m/Y').' ('.$beneficiario->fecha_nacimiento->age.' años)' : '—' }}</div>
            <div class="col-md-3"><div class="text-muted small">Sexo</div>{{ ['M' => 'Masculino', 'F' => 'Femenino', 'Otro' => 'Otro'][$beneficiario->sexo] ?? '—' }}</div>
            <div class="col-md-3"><div class="text-muted small">Teléfono</div>{{ $beneficiario->telefono ?: '—' }}</div>
            <div class="col-md-6"><div class="text-muted small">Dirección</div>{{ $beneficiario->direccion ?: '—' }}</div>
            <div class="col-md-3"><div class="text-muted small">Familiares en el hogar</div>{{ $beneficiario->num_familiares }}</div>
            <div class="col-md-3"><div class="text-muted small">Registrado el</div>{{ $beneficiario->created_at?->format('d/m/Y') }}</div>
            <div class="col-12"><div class="text-muted small">Situación socioeconómica</div>{{ $beneficiario->situacion ?: '—' }}</div>
            <div class="col-12"><div class="text-muted small">Observaciones</div>{{ $beneficiario->observaciones ?: '—' }}</div>
        </div>
    </div>
</div>

{{-- RF03: Necesidades --}}
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">Tipo de ayuda que necesita</div>
    <div class="card-body">
        @if ($u->tienePermiso('tipos_ayuda.crear'))
        <form method="POST" action="{{ route('necesidades.store', $beneficiario) }}" class="row g-2 mb-3">
            @csrf
            <div class="col-md-3">
                <select name="tipo_ayuda_id" class="form-select @error('tipo_ayuda_id') is-invalid @enderror" required>
                    <option value="">— Tipo de ayuda —</option>
                    @foreach ($tiposAyuda as $t)
                        <option value="{{ $t->id }}" @selected(old('tipo_ayuda_id') == $t->id)>{{ $t->nombre }}</option>
                    @endforeach
                </select>
                @error('tipo_ayuda_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_registro" value="{{ old('fecha_registro', date('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="observacion" value="{{ old('observacion') }}" class="form-control" placeholder="Observación (opcional)">
            </div>
            <div class="col-md-2"><button class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i>Agregar</button></div>
        </form>
        @endif

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>Fecha</th><th>Tipo de ayuda</th><th>Observación</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @forelse ($necesidades as $n)
                    <tr>
                        <td>{{ $n->fecha_registro->format('d/m/Y') }}</td>
                        <td>{{ $n->tipoAyuda->nombre }}</td>
                        <td>{{ $n->observacion ?: '—' }}</td>
                        <td><span class="badge {{ $n->estado === 'pendiente' ? 'text-bg-warning' : 'text-bg-success' }}">{{ ucfirst($n->estado) }}</span></td>
                        <td class="text-end">
                            @if ($n->estado === 'pendiente' && $u->tienePermiso('tipos_ayuda.crear'))
                                <form method="POST" action="{{ route('necesidades.atender', $n) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-success">Marcar atendida</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">No hay necesidades registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- RF06: Historial --}}
@if ($u->tienePermiso('historial.ver'))
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">Historial de ayudas recibidas</div>
    <div class="card-body">
        <div class="table-responsive mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>Fecha</th><th>Tipo</th><th>Descripción</th><th>Registró</th></tr></thead>
                <tbody>
                @forelse ($ayudas as $a)
                    <tr>
                        <td>{{ $a->fecha_entrega->format('d/m/Y') }}</td>
                        <td>{{ $a->tipoAyuda->nombre }}</td>
                        <td>{{ $a->descripcion ?: '—' }}</td>
                        <td>{{ $a->usuario->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Aún no ha recibido ayudas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <h6 class="fw-semibold">Entregas de alimentos / almuerzos</h6>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>Fecha</th><th>Tipo</th><th>Raciones</th><th>Registró</th></tr></thead>
                <tbody>
                @forelse ($alimentos as $e)
                    <tr>
                        <td>{{ $e->fecha->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($e->tipo_entrega) }}</td>
                        <td>{{ $e->raciones }}</td>
                        <td>{{ $e->usuario->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Sin entregas de alimentos.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
