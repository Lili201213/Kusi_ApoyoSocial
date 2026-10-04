<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    private const ROL_ADMIN = 1;

    /** Permisos que SOLO tiene el administrador (no se pueden asignar a otros roles) */
    public static function soloAdmin(string $nombre): bool
    {
        return str_ends_with($nombre, '.eliminar')
            || in_array($nombre, ['usuarios.gestionar', 'roles.gestionar', 'donaciones.anular'], true);
    }

    private static function grupo(string $nombre): string
    {
        return match (explode('.', $nombre)[0]) {
            'beneficiarios', 'necesidades' => 'Beneficiarios',
            'tipos_ayuda', 'ayudas', 'alimentos', 'historial' => 'Ayudas y alimentos',
            'donaciones', 'donantes' => 'Donaciones',
            'productos', 'inventario' => 'Inventario',
            'reportes', 'estadisticas' => 'Reportes y estadísticas',
            default => 'Administración',
        };
    }

    public function index()
    {
        $roles = Rol::with('permisos')->orderBy('id')->get();

        $orden = ['Beneficiarios', 'Ayudas y alimentos', 'Donaciones', 'Inventario', 'Reportes y estadísticas', 'Administración'];
        $grupos = Permiso::orderBy('id')->get()
            ->groupBy(fn ($p) => self::grupo($p->nombre))
            ->sortBy(fn ($items, $nombre) => array_search($nombre, $orden));

        return view('roles.index', ['roles' => $roles, 'grupos' => $grupos]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['array'],
            'permisos.*.*' => ['integer', 'exists:permisos,id'],
        ]);

        $asignables = Permiso::all()
            ->reject(fn ($p) => self::soloAdmin($p->nombre))
            ->pluck('id')->map(fn ($v) => (int) $v)->all();

        DB::transaction(function () use ($request, $asignables) {
            foreach (Rol::where('id', '!=', self::ROL_ADMIN)->get() as $rol) {
                $marcados = array_map('intval', $request->input("permisos.{$rol->id}", []));
                $rol->permisos()->sync(array_values(array_intersect($marcados, $asignables)));
            }
        });

        return back()->with('exito', 'Permisos actualizados. Los cambios se aplican de inmediato.');
    }
}
