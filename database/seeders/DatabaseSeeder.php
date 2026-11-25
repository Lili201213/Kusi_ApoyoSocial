<?php

namespace Database\Seeders;

use App\Models\CategoriaProducto;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\TipoAyuda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $rolesData = [
            1 => [
                'nombre' => 'Administrador',
                'descripcion' => 'Gestiona usuarios, roles, permisos y supervisa toda la información',
            ],
            2 => [
                'nombre' => 'Personal de Beneficencia',
                'descripcion' => 'Registra y gestiona beneficiarios, ayudas, entregas, donaciones e inventario',
            ],
            3 => [
                'nombre' => 'Consulta / Supervisor',
                'descripcion' => 'Consulta información, reportes y estadísticas sin modificar registros',
            ],
        ];

        foreach ($rolesData as $id => $data) {
            Rol::updateOrCreate(['id' => $id], $data);
        }

        // 2. Permisos
        $permisosData = [
            1 => ['nombre' => 'usuarios.gestionar', 'descripcion' => 'Gestionar usuarios'],
            2 => ['nombre' => 'roles.gestionar', 'descripcion' => 'Gestionar roles y permisos'],
            3 => ['nombre' => 'beneficiarios.crear', 'descripcion' => 'Registrar beneficiarios'],
            4 => ['nombre' => 'beneficiarios.ver', 'descripcion' => 'Consultar beneficiarios'],
            5 => ['nombre' => 'beneficiarios.editar', 'descripcion' => 'Modificar beneficiarios'],
            6 => ['nombre' => 'tipos_ayuda.crear', 'descripcion' => 'Registrar tipo de ayuda'],
            7 => ['nombre' => 'ayudas.crear', 'descripcion' => 'Registrar ayudas entregadas'],
            8 => ['nombre' => 'alimentos.crear', 'descripcion' => 'Registrar alimentos / almuerzos'],
            9 => ['nombre' => 'historial.ver', 'descripcion' => 'Consultar historial de ayudas'],
            10 => ['nombre' => 'donaciones.crear', 'descripcion' => 'Registrar donaciones'],
            11 => ['nombre' => 'donaciones.ver', 'descripcion' => 'Consultar donaciones'],
            12 => ['nombre' => 'productos.crear', 'descripcion' => 'Registrar productos en inventario'],
            13 => ['nombre' => 'inventario.ver', 'descripcion' => 'Consultar existencias'],
            14 => ['nombre' => 'inventario.entrada', 'descripcion' => 'Registrar entradas de productos'],
            15 => ['nombre' => 'inventario.salida', 'descripcion' => 'Registrar salidas de productos'],
            16 => ['nombre' => 'reportes.generar', 'descripcion' => 'Generar reportes'],
            17 => ['nombre' => 'estadisticas.ver', 'descripcion' => 'Visualizar estadísticas'],
            18 => ['nombre' => 'beneficiarios.eliminar', 'descripcion' => 'Eliminar beneficiarios'],
            19 => ['nombre' => 'ayudas.eliminar', 'descripcion' => 'Eliminar ayudas entregadas'],
            20 => ['nombre' => 'alimentos.eliminar', 'descripcion' => 'Eliminar entregas de alimentos'],
            21 => ['nombre' => 'necesidades.eliminar', 'descripcion' => 'Eliminar necesidades de ayuda'],
            22 => ['nombre' => 'tipos_ayuda.eliminar', 'descripcion' => 'Eliminar tipos de ayuda sin uso'],
            23 => ['nombre' => 'productos.eliminar', 'descripcion' => 'Eliminar productos y categorías sin uso'],
            24 => ['nombre' => 'donantes.eliminar', 'descripcion' => 'Eliminar donantes sin donaciones'],
            25 => ['nombre' => 'donaciones.anular', 'descripcion' => 'Anular donaciones y revertir stock'],
        ];

        foreach ($permisosData as $id => $data) {
            Permiso::updateOrCreate(['id' => $id], $data);
        }

        // 3. Asignación de Permisos a Roles
        // Admin: todos los permisos (1 a 25)
        $adminPerms = range(1, 25);
        // Personal: 3 a 17
        $personalPerms = range(3, 17);
        // Supervisor: 4, 9, 11, 13, 16, 17
        $supervisorPerms = [4, 9, 11, 13, 16, 17];

        $asignaciones = [
            1 => $adminPerms,
            2 => $personalPerms,
            3 => $supervisorPerms,
        ];

        foreach ($asignaciones as $rolId => $perms) {
            foreach ($perms as $permId) {
                DB::table('permiso_rol')->updateOrInsert(
                    ['rol_id' => $rolId, 'permiso_id' => $permId]
                );
            }
        }

        // 4. Usuario Administrador Predeterminado
        User::firstOrCreate(
            ['usuario' => 'admin'],
            [
                'name' => 'Administrador KUSI',
                'email' => 'admin@kusi.pe',
                'telefono' => '999888777',
                'password' => Hash::make('Admin123*'),
                'rol_id' => 1,
                'activo' => true,
                'email_verified_at' => now(),
            ]
        );

        // 5. Categorías de producto iniciales
        $categorias = ['Abarrotes', 'Lácteos', 'Cereales y Menestras', 'Enlatados', 'Higiene y Aseo'];
        foreach ($categorias as $cat) {
            CategoriaProducto::firstOrCreate(['nombre' => $cat]);
        }

        // 6. Tipos de ayuda iniciales
        $tipos = [
            ['nombre' => 'Canasta de Alimentos', 'descripcion' => 'Canasta con productos básicos de primera necesidad'],
            ['nombre' => 'Almuerzo Comunitario', 'descripcion' => 'Ración diaria de almuerzo preparado'],
            ['nombre' => 'Prendas de Abrigo', 'descripcion' => 'Ropa y mantas para temporada de frío'],
            ['nombre' => 'Apoyo Médico / Medicinas', 'descripcion' => 'Medicamentos y atención básica'],
            ['nombre' => 'Kit Escolar', 'descripcion' => 'Cuadernos y útiles para estudiantes'],
        ];
        foreach ($tipos as $tipo) {
            TipoAyuda::firstOrCreate(['nombre' => $tipo['nombre']], $tipo);
        }
    }
}
