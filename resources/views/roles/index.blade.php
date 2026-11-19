<x-layout>
    <x-slot:title>{{ __('Roles y permisos') }}</x-slot>

<h2 class="mb-1">{{ __('Roles y permisos') }}</h2>
<p class="text-muted">{{ __('Marca qué puede hacer cada rol. Los cambios se aplican de inmediato a todos los usuarios de ese rol.') }}</p>

<div class="alert alert-info py-2 small">
    <i class="bi bi-info-circle me-1"></i>
    {!! __('El Administrador siempre tiene todos los permisos. Los permisos con :candado (gestionar usuarios y roles, eliminar y anular) son exclusivos del Administrador.', ['candado' => '<i class="bi bi-lock-fill"></i>']) !!}
</div>

<form method="POST" action="{{ route('roles.update') }}">
    @csrf @method('PUT')
    <div class="card shadow-sm mb-3">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:280px">{{ __('Funcionalidad') }}</th>
                        @foreach ($roles as $r)<th class="text-center">{{ $r->nombre }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach ($grupos as $grupo => $permisos)
                    <tr class="table-secondary"><td colspan="{{ $roles->count() + 1 }}" class="fw-semibold">{{ __($grupo) }}</td></tr>
                    @foreach ($permisos as $p)
                        @php $bloqueado = \App\Http\Controllers\RolController::soloAdmin($p->nombre); @endphp
                        <tr>
                            <td>
                                {{ __($p->descripcion) }}
                                @if ($bloqueado) <i class="bi bi-lock-fill text-muted ms-1" title="{{ __('Solo Administrador') }}"></i> @endif
                                <div class="text-muted small"><code>{{ $p->nombre }}</code></div>
                            </td>
                            @foreach ($roles as $r)
                                @php $esAdmin = $r->id === 1; @endphp
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input"
                                           @if (! $esAdmin && ! $bloqueado) name="permisos[{{ $r->id }}][]" value="{{ $p->id }}" @endif
                                           @checked($esAdmin || $r->permisos->contains('id', $p->id))
                                           @disabled($esAdmin || $bloqueado)>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>{{ __('Guardar permisos') }}</button>
</form>
</x-layout>
