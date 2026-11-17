<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BeneficiarioApiController;
use App\Http\Controllers\Api\DonacionApiController;
use App\Http\Controllers\Api\ProductoApiController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/beneficiarios', [BeneficiarioApiController::class, 'index']);
Route::get('/beneficiarios/{id}', [BeneficiarioApiController::class, 'show']);

Route::get('/donaciones', [DonacionApiController::class, 'index']);
Route::get('/donaciones/{id}', [DonacionApiController::class, 'show']);

Route::get('/productos', [ProductoApiController::class, 'index']);
Route::get('/productos/{id}', [ProductoApiController::class, 'show']);
