<x-layout>
    <x-slot:title>{{ $beneficiario->nombres }} {{ $beneficiario->apellidos }}</x-slot>

@php $u = auth()->user(); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h2 class="mb-0">{{ $beneficiario->nombres }} {{ $beneficiario->apellidos }}</h2>
        <span class="badge {{ $beneficiario->estado === 'activo' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $beneficiario->estado === 'activo' ? __('Activo') : __('Inactivo') }}</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('beneficiarios.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>{{ __('Volver') }}</a>
        @if ($beneficiario->estado === 'activo' && $u->tienePermiso('ayudas.crear'))
            <a href="{{ route('ayudas.create', ['beneficiario' => $beneficiario->id]) }}" class="btn btn-success"><i class="bi bi-box2-heart me-1"></i>{{ __('Registrar ayuda') }}</a>
        @endif
        @if ($beneficiario->estado === 'activo' && $u->tienePermiso('alimentos.crear'))
            <a href="{{ route('alimentos.create', ['beneficiario' => $beneficiario->id]) }}" class="btn btn-info"><i class="bi bi-egg-fried me-1"></i>{{ __('Registrar alimentos') }}</a>
        @endif
        @if ($u->tienePermiso('beneficiarios.editar'))
            <a href="{{ route('beneficiarios.edit', $beneficiario) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>{{ __('Editar') }}</a>
        @endif
        @if ($u->tienePermiso('beneficiarios.eliminar'))
            <form method="POST" action="{{ route('beneficiarios.destroy', $beneficiario) }}"
                  onsubmit="return confirm('{{ __('¿Eliminar este beneficiario? Esta acción no se puede deshacer.') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>{{ __('Eliminar') }}</button>
            </form>
        @endif
    </div>
</div>

{{-- Datos personales --}}
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">{{ __('Datos personales') }}</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><div class="text-muted small">{{ __('DNI') }}</div>{{ $beneficiario->dni }}</div>
            <div class="col-md-3"><div class="text-muted small">{{ __('Fecha de nacimiento') }}</div>
                {{ $beneficiario->fecha_nacimiento ? $beneficiario->fecha_nacimiento->format('d/m/Y').' ('.__(':age años', ['age' => $beneficiario->fecha_nacimiento->age]).')' : '—' }}</div>
            <div class="col-md-3"><div class="text-muted small">{{ __('Sexo') }}</div>{{ ['M' => __('Masculino'), 'F' => __('Femenino'), 'Otro' => __('Otro')][$beneficiario->sexo] ?? '—' }}</div>
            <div class="col-md-3"><div class="text-muted small">{{ __('Teléfono') }}</div>{{ $beneficiario->telefono ?: '—' }}</div>
            <div class="col-md-6"><div class="text-muted small">{{ __('Dirección') }}</div>{{ $beneficiario->direccion ?: '—' }}</div>
            <div class="col-md-3"><div class="text-muted small">{{ __('Familiares en el hogar') }}</div>{{ $beneficiario->num_familiares }}</div>
            <div class="col-md-3"><div class="text-muted small">{{ __('Registrado el') }}</div>{{ $beneficiario->created_at?->format('d/m/Y') }}</div>
            <div class="col-12"><div class="text-muted small">{{ __('Situación socioeconómica') }}</div>{{ $beneficiario->situacion ?: '—' }}</div>
            <div class="col-12"><div class="text-muted small">{{ __('Observaciones') }}</div>{{ $beneficiario->observaciones ?: '—' }}</div>
        </div>
    </div>
</div>

{{-- RF03: Necesidades --}}
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">{{ __('Tipo de ayuda que necesita') }}</div>
    <div class="card-body">
        @if ($u->tienePermiso('tipos_ayuda.crear'))
        <form method="POST" action="{{ route('necesidades.store', $beneficiario) }}" class="row g-2 mb-3">
            @csrf
            <div class="col-md-3">
                <select name="tipo_ayuda_id" class="form-select @error('tipo_ayuda_id') is-invalid @enderror" required>
                    <option value="">— {{ __('Tipo de ayuda') }} —</option>
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
                <input type="text" name="observacion" value="{{ old('observacion') }}" class="form-control" placeholder="{{ __('Observación (opcional)') }}">
            </div>
            <div class="col-md-2"><button class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i>{{ __('Agregar') }}</button></div>
        </form>
        @endif

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>{{ __('Fecha') }}</th><th>{{ __('Tipo de ayuda') }}</th><th>{{ __('Observación') }}</th><th>{{ __('Estado') }}</th><th></th></tr></thead>
                <tbody>
                @forelse ($necesidades as $n)
                    <tr>
                        <td>{{ $n->fecha_registro->format('d/m/Y') }}</td>
                        <td>{{ $n->tipoAyuda->nombre }}</td>
                        <td>{{ $n->observacion ?: '—' }}</td>
                        <td><span class="badge {{ $n->estado === 'pendiente' ? 'text-bg-warning' : 'text-bg-success' }}">{{ $n->estado === 'pendiente' ? __('Pendiente') : __('Atendida') }}</span></td>
                        <td class="text-end text-nowrap">
                            @if ($n->estado === 'pendiente' && $u->tienePermiso('tipos_ayuda.crear'))
                                <form method="POST" action="{{ route('necesidades.atender', $n) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-success">{{ __('Marcar atendida') }}</button>
                                </form>
                            @endif
                            @if ($u->tienePermiso('necesidades.eliminar'))
                                <form method="POST" action="{{ route('necesidades.destroy', $n) }}" class="d-inline"
                                      onsubmit="return confirm('{{ __('¿Eliminar esta necesidad?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="{{ __('Eliminar') }}"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">{{ __('No hay necesidades registradas.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- RF06: Historial --}}
@if ($u->tienePermiso('historial.ver'))
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">{{ __('Historial de ayudas recibidas') }}</div>
    <div class="card-body">
        <div class="table-responsive mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>{{ __('Fecha') }}</th><th>{{ __('Tipo') }}</th><th>{{ __('Descripción') }}</th><th>{{ __('Registró') }}</th></tr></thead>
                <tbody>
                @forelse ($ayudas as $a)
                    <tr>
                        <td>{{ $a->fecha_entrega->format('d/m/Y') }}</td>
                        <td>{{ $a->tipoAyuda->nombre }}</td>
                        <td>{{ $a->descripcion ?: '—' }}</td>
                        <td>{{ $a->usuario->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">{{ __('Aún no ha recibido ayudas.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <h6 class="fw-semibold">{{ __('Entregas de alimentos / almuerzos') }}</h6>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>{{ __('Fecha') }}</th><th>{{ __('Tipo') }}</th><th>{{ __('Raciones') }}</th><th>{{ __('Registró') }}</th></tr></thead>
                <tbody>
                @forelse ($alimentos as $e)
                    <tr>
                        <td>{{ $e->fecha->format('d/m/Y') }}</td>
                        <td>{{ $e->tipo_entrega === 'almuerzo' ? __('Almuerzo') : ($e->tipo_entrega === 'despensa' ? __('Despensa') : ucfirst($e->tipo_entrega)) }}</td>
                        <td>{{ $e->raciones }}</td>
                        <td>{{ $e->usuario->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">{{ __('Sin entregas de alimentos.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
</x-layout>
