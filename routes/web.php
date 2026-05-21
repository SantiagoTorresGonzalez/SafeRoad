<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PanelAutoridadController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PlanificadorController;

// ========================================
// RUTA PRINCIPAL — Landing Page
// ========================================
Route::get('/', function () {
    return view('home');
})->name('home');

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
// CHATBOT — Solo usuarios autenticados
// ========================================
Route::middleware(['auth'])->group(function () {
    Route::post('/chatbot/premium', [ChatbotController::class, 'premium'])->name('chatbot.premium');
});

// ========================================
// SAFEROAD SC — RUTAS PANEL AUTORIDAD
// ========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/panel-autoridad', [PanelAutoridadController::class, 'index'])->name('panel.index');
    Route::patch('/panel-autoridad/{id}', [PanelAutoridadController::class, 'actualizar'])->name('panel.actualizar');
});

Route::middleware(['auth', 'role:planificador_territorial'])->group(function () {
    Route::get('/planificador', [PlanificadorController::class, 'index'])->name('planificador.index');
    Route::patch('/planificador/{id}', [PlanificadorController::class, 'actualizar'])->name('planificador.actualizar');
});