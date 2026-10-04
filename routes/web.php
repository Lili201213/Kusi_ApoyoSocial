<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AyudaEntregadaController;
use App\Http\Controllers\BeneficiarioController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonacionController;
use App\Http\Controllers\DonanteController;
use App\Http\Controllers\EntregaAlimentoController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RespaldoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\UsuarioActivo;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TipoAyudaController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Zona protegida
Route::middleware(['auth', UsuarioActivo::class])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------- Beneficiarios ----------
    // OJO: 'crear' va antes que '{beneficiario}' para que no se confunda con un id.
    Route::get('/beneficiarios', [BeneficiarioController::class, 'index'])
        ->middleware('permiso:beneficiarios.ver')->name('beneficiarios.index');
    Route::get('/beneficiarios/crear', [BeneficiarioController::class, 'create'])
        ->middleware('permiso:beneficiarios.crear')->name('beneficiarios.create');
    Route::post('/beneficiarios', [BeneficiarioController::class, 'store'])
        ->middleware('permiso:beneficiarios.crear')->name('beneficiarios.store');
    Route::get('/beneficiarios/{beneficiario}', [BeneficiarioController::class, 'show'])
        ->middleware('permiso:beneficiarios.ver')->name('beneficiarios.show');
    Route::get('/beneficiarios/{beneficiario}/editar', [BeneficiarioController::class, 'edit'])
        ->middleware('permiso:beneficiarios.editar')->name('beneficiarios.edit');
    Route::put('/beneficiarios/{beneficiario}', [BeneficiarioController::class, 'update'])
        ->middleware('permiso:beneficiarios.editar')->name('beneficiarios.update');

    Route::delete('/beneficiarios/{beneficiario}', [BeneficiarioController::class, 'destroy'])
        ->middleware('permiso:beneficiarios.eliminar')->name('beneficiarios.destroy');

    // Necesidades de ayuda (RF03)
    Route::post('/beneficiarios/{beneficiario}/necesidades', [BeneficiarioController::class, 'storeNecesidad'])
        ->middleware('permiso:tipos_ayuda.crear')->name('necesidades.store');
    Route::patch('/necesidades/{necesidad}/atender', [BeneficiarioController::class, 'atenderNecesidad'])
        ->middleware('permiso:tipos_ayuda.crear')->name('necesidades.atender');

    Route::delete('/necesidades/{necesidad}', [BeneficiarioController::class, 'destroyNecesidad'])
        ->middleware('permiso:necesidades.eliminar')->name('necesidades.destroy');

    // ---------- Tipos de ayuda (catálogo) ----------
    Route::get('/tipos-ayuda', [TipoAyudaController::class, 'index'])
        ->middleware('permiso:tipos_ayuda.crear')->name('tipos_ayuda.index');
    Route::post('/tipos-ayuda', [TipoAyudaController::class, 'store'])
        ->middleware('permiso:tipos_ayuda.crear')->name('tipos_ayuda.store');
    Route::patch('/tipos-ayuda/{tipoAyuda}/estado', [TipoAyudaController::class, 'cambiarEstado'])
        ->middleware('permiso:tipos_ayuda.crear')->name('tipos_ayuda.estado');

    Route::delete('/tipos-ayuda/{tipoAyuda}', [TipoAyudaController::class, 'destroy'])
        ->middleware('permiso:tipos_ayuda.eliminar')->name('tipos_ayuda.destroy');

    // ---------- Ayudas entregadas (RF04) ----------
    Route::get('/ayudas', [AyudaEntregadaController::class, 'index'])
        ->middleware('permiso:historial.ver')->name('ayudas.index');
    Route::get('/ayudas/crear', [AyudaEntregadaController::class, 'create'])
        ->middleware('permiso:ayudas.crear')->name('ayudas.create');
    Route::post('/ayudas', [AyudaEntregadaController::class, 'store'])
        ->middleware('permiso:ayudas.crear')->name('ayudas.store');

    Route::delete('/ayudas/{ayuda}', [AyudaEntregadaController::class, 'destroy'])
        ->middleware('permiso:ayudas.eliminar')->name('ayudas.destroy');

    // ---------- Alimentos / almuerzos (RF05) ----------
    Route::get('/alimentos', [EntregaAlimentoController::class, 'index'])
        ->middleware('permiso:historial.ver')->name('alimentos.index');
    Route::get('/alimentos/crear', [EntregaAlimentoController::class, 'create'])
        ->middleware('permiso:alimentos.crear')->name('alimentos.create');
    Route::post('/alimentos', [EntregaAlimentoController::class, 'store'])
        ->middleware('permiso:alimentos.crear')->name('alimentos.store');

    Route::delete('/alimentos/{entrega}', [EntregaAlimentoController::class, 'destroy'])
        ->middleware('permiso:alimentos.eliminar')->name('alimentos.destroy');

    // ---------- Donantes ----------
    Route::get('/donantes', [DonanteController::class, 'index'])
        ->middleware('permiso:donaciones.ver')->name('donantes.index');
    Route::get('/donantes/crear', [DonanteController::class, 'create'])
        ->middleware('permiso:donaciones.crear')->name('donantes.create');
    Route::post('/donantes', [DonanteController::class, 'store'])
        ->middleware('permiso:donaciones.crear')->name('donantes.store');
    Route::get('/donantes/{donante}/editar', [DonanteController::class, 'edit'])
        ->middleware('permiso:donaciones.crear')->name('donantes.edit');
    Route::put('/donantes/{donante}', [DonanteController::class, 'update'])
        ->middleware('permiso:donaciones.crear')->name('donantes.update');
    Route::delete('/donantes/{donante}', [DonanteController::class, 'destroy'])
        ->middleware('permiso:donantes.eliminar')->name('donantes.destroy');

    // ---------- Donaciones (RF07, RF08, RF09) ----------
    Route::get('/donaciones', [DonacionController::class, 'index'])
        ->middleware('permiso:donaciones.ver')->name('donaciones.index');
    Route::get('/donaciones/crear', [DonacionController::class, 'create'])
        ->middleware('permiso:donaciones.crear')->name('donaciones.create');
    Route::post('/donaciones', [DonacionController::class, 'store'])
        ->middleware('permiso:donaciones.crear')->name('donaciones.store');
    Route::get('/donaciones/{donacion}', [DonacionController::class, 'show'])
        ->middleware('permiso:donaciones.ver')->name('donaciones.show');
    Route::post('/donaciones/{donacion}/anular', [DonacionController::class, 'anular'])
        ->middleware('permiso:donaciones.anular')->name('donaciones.anular');

    // ---------- Categorías de producto ----------
    Route::get('/categorias', [CategoriaProductoController::class, 'index'])
        ->middleware('permiso:productos.crear')->name('categorias.index');
    Route::post('/categorias', [CategoriaProductoController::class, 'store'])
        ->middleware('permiso:productos.crear')->name('categorias.store');
    Route::delete('/categorias/{categoria}', [CategoriaProductoController::class, 'destroy'])
        ->middleware('permiso:productos.eliminar')->name('categorias.destroy');

    // ---------- Inventario / productos (RF10, RF11) ----------
    Route::get('/productos', [ProductoController::class, 'index'])
        ->middleware('permiso:inventario.ver')->name('productos.index');
    Route::get('/productos/crear', [ProductoController::class, 'create'])
        ->middleware('permiso:productos.crear')->name('productos.create');
    Route::post('/productos', [ProductoController::class, 'store'])
        ->middleware('permiso:productos.crear')->name('productos.store');
    Route::get('/productos/{producto}', [ProductoController::class, 'show'])
        ->middleware('permiso:inventario.ver')->name('productos.show');
    Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])
        ->middleware('permiso:productos.crear')->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])
        ->middleware('permiso:productos.crear')->name('productos.update');
    Route::patch('/productos/{producto}/estado', [ProductoController::class, 'cambiarEstado'])
        ->middleware('permiso:productos.crear')->name('productos.estado');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
        ->middleware('permiso:productos.eliminar')->name('productos.destroy');

    // ---------- Movimientos de inventario (RF12, RF13) ----------
    Route::get('/movimientos', [MovimientoInventarioController::class, 'index'])
        ->middleware('permiso:inventario.ver')->name('movimientos.index');
    Route::get('/movimientos/entrada', [MovimientoInventarioController::class, 'create'])
        ->defaults('tipo', 'entrada')->middleware('permiso:inventario.entrada')->name('movimientos.entrada');
    Route::post('/movimientos/entrada', [MovimientoInventarioController::class, 'store'])
        ->defaults('tipo', 'entrada')->middleware('permiso:inventario.entrada')->name('movimientos.entrada.store');
    Route::get('/movimientos/salida', [MovimientoInventarioController::class, 'create'])
        ->defaults('tipo', 'salida')->middleware('permiso:inventario.salida')->name('movimientos.salida');
    Route::post('/movimientos/salida', [MovimientoInventarioController::class, 'store'])
        ->defaults('tipo', 'salida')->middleware('permiso:inventario.salida')->name('movimientos.salida.store');

    // ---------- Reportes (RF14) y Estadísticas (RF15) ----------
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware('permiso:reportes.generar')->name('reportes.index');
    Route::get('/reportes/{tipo}', [ReporteController::class, 'ver'])
        ->where('tipo', 'beneficiarios|ayudas|alimentos|donaciones|inventario')
        ->middleware('permiso:reportes.generar')->name('reportes.ver');
    Route::get('/reportes/{tipo}/exportar', [ReporteController::class, 'exportar'])
        ->where('tipo', 'beneficiarios|ayudas|alimentos|donaciones|inventario')
        ->middleware('permiso:reportes.generar')->name('reportes.exportar');
    Route::get('/estadisticas', [EstadisticaController::class, 'index'])
        ->middleware('permiso:estadisticas.ver')->name('estadisticas.index');

    // ---------- Mi cuenta (cualquier usuario) ----------
    Route::get('/cuenta', [CuentaController::class, 'edit'])->name('cuenta.edit');
    Route::put('/cuenta', [CuentaController::class, 'update'])->name('cuenta.update');

    // ---------- Usuarios (RF16) ----------
    Route::get('/usuarios', [UsuarioController::class, 'index'])
        ->middleware('permiso:usuarios.gestionar')->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])
        ->middleware('permiso:usuarios.gestionar')->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])
        ->middleware('permiso:usuarios.gestionar')->name('usuarios.store');
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])
        ->middleware('permiso:usuarios.gestionar')->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->middleware('permiso:usuarios.gestionar')->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])
        ->middleware('permiso:usuarios.gestionar')->name('usuarios.estado');

    // ---------- Copias de seguridad (RNF10) ----------
    Route::get('/respaldos', [RespaldoController::class, 'index'])
        ->middleware('permiso:usuarios.gestionar')->name('respaldos.index');
    Route::post('/respaldos/descargar', [RespaldoController::class, 'descargar'])
        ->middleware('permiso:usuarios.gestionar')->name('respaldos.descargar');

    // ---------- Roles y permisos (RF18, RNF03) ----------
    Route::get('/roles', [RolController::class, 'index'])
        ->middleware('permiso:roles.gestionar')->name('roles.index');
    Route::put('/roles/permisos', [RolController::class, 'update'])
        ->middleware('permiso:roles.gestionar')->name('roles.update');
});
