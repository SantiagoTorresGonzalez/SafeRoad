<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PanelAutoridadController;

// ========================================
// RUTA PRINCIPAL
// ========================================
Route::get('/', fn() => redirect()->route('login'));

// ========================================
// AUTENTICACIÓN
// ========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [CrearUsuario::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [CrearUsuario::class, 'register']);

// ========================================
// SAFEROAD SC — RUTAS PÚBLICAS
// ========================================
Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');
Route::post('/mapa/reportar', [MapaController::class, 'store'])->name('mapa.store');

// ========================================
// SAFEROAD SC — RUTAS PANEL AUTORIDAD
// ========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/panel-autoridad', [PanelAutoridadController::class, 'index'])->name('panel.index');
    Route::patch('/panel-autoridad/{id}', [PanelAutoridadController::class, 'actualizar'])->name('panel.actualizar');
});