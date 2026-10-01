<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BeneficiarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Zona protegida
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

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

    // Necesidades de ayuda (RF03)
    Route::post('/beneficiarios/{beneficiario}/necesidades', [BeneficiarioController::class, 'storeNecesidad'])
        ->middleware('permiso:tipos_ayuda.crear')->name('necesidades.store');
    Route::patch('/necesidades/{necesidad}/atender', [BeneficiarioController::class, 'atenderNecesidad'])
        ->middleware('permiso:tipos_ayuda.crear')->name('necesidades.atender');

    // ---------- Usuarios (en construcción) ----------
    Route::get('/usuarios', fn () => view('en_construccion', ['titulo' => 'Usuarios']))
        ->middleware('permiso:usuarios.gestionar')
        ->name('usuarios.index');
});
