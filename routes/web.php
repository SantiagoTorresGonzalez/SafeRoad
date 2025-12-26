<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CuentaCobroController;
use App\Http\Controllers\ItemCuentaCobroController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\SoporteController;
use App\Http\Controllers\InteraccionController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\AprobacionController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\TerceroController;
use App\Http\Controllers\DianController;

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

// RUTA TEMPORAL DE PRUEBA (eliminar después)
Route::get('/test-users', function () {
    $users = \App\Models\User::with('role')->get();
    echo "<h1>Usuarios Registrados</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user->id}</td>";
        echo "<td>{$user->name}</td>";
        echo "<td>{$user->email}</td>";
        echo "<td>" . ($user->role ? $user->role->name : 'Sin rol') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p>Total: " . $users->count() . " usuarios</p>";
});

// ========================================
// RUTAS PROTEGIDAS POR AUTH
// ========================================
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/sugerencia', [DashboardController::class, 'enviarSugerencia'])->name('dashboard.sugerencia');
    Route::post('/dashboard/tesoreria/enviar/{id}', [DashboardController::class, 'enviarCuentaCliente'])->name('tesoreria.enviar');

    // NOTIFICACIONES
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/notificaciones/{id}/visitar', [NotificacionController::class, 'visitar'])->name('notificaciones.visitar');
    Route::post('/notificaciones/{id}/marcar-leida', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcarLeida');
    Route::post('/notificaciones/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.marcarTodasLeidas');
    Route::get('/notificaciones/preferencias', [NotificacionController::class, 'preferencias'])->name('notificaciones.preferencias');
    Route::post('/notificaciones/preferencias', [NotificacionController::class, 'guardarPreferencias'])->name('notificaciones.guardarPreferencias');

    // ========================================
    // DOCUMENTOS (gestión de archivos)
    // ========================================
    Route::prefix('documentos')->name('documentos.')->group(function () {
        Route::get('cuentas/{cuentaCobroId}', [DocumentoController::class, 'index'])->name('index');
        Route::get('crear/{cuentaCobroId}', [DocumentoController::class, 'create'])->name('create');
        Route::post('guardar/{cuentaCobroId}', [DocumentoController::class, 'store'])->name('store');
        Route::get('{documentoId}/descargar', [DocumentoController::class, 'descargar'])->name('descargar');
        Route::get('{documentoId}/ver', [DocumentoController::class, 'ver'])->name('ver');
        Route::delete('{documentoId}', [DocumentoController::class, 'destroy'])->name('destroy');
        Route::post('{documentoId}/version', [DocumentoController::class, 'crearVersion'])->name('crearVersion');
        Route::get('{documentoId}/versiones', [DocumentoController::class, 'versiones'])->name('versiones');
        Route::post('{documentoId}/archivar', [DocumentoController::class, 'archivar'])->name('archivar');
        Route::post('{documentoId}/desarchivizar', [DocumentoController::class, 'desarchivizar'])->name('desarchivizar');
    });

    // ========================================
    // APROBACIONES MEJORADAS
    // ========================================
    // Allow access by role OR by permission (approve/reject)
    Route::middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':administrador,tesoreria,admin_programa,approve_cuenta_cobro,aprobar,reject_cuenta_cobro,rechazar'])
        ->prefix('aprobaciones')->name('aprobaciones.')->group(function () {
        Route::post('{cuentaId}/modal', [AprobacionController::class, 'mostrarModalAprobacion'])->name('modal');
        Route::post('{cuentaId}/enviar-siguiente', [AprobacionController::class, 'enviarAlSiguiente'])->name('enviarSiguiente');
        Route::post('{cuentaId}/rechazar', [AprobacionController::class, 'rechazar'])->name('rechazar');
        Route::post('{cuentaId}/devolver-anterior', [AprobacionController::class, 'devolverAnterior'])->name('devolverAnterior');
        Route::post('{cuentaId}/devolver-correccion', [AprobacionController::class, 'devolverCorreccion'])->name('devolverCorreccion');
        Route::post('{cuentaId}/interaccion', [AprobacionController::class, 'agregarInteraccion'])->name('agregarInteraccion');
        Route::get('{cuentaId}/historial', [AprobacionController::class, 'obtenerHistorial'])->name('obtenerHistorial');
    });

    // ========================================
    // TERCEROS (CRUD + AJAX)
    // ========================================
    Route::get('/terceros', [TerceroController::class, 'index'])->name('terceros.index');
    Route::get('/terceros/search', [TerceroController::class, 'search'])->name('terceros.search');
    Route::post('/terceros/store', [TerceroController::class, 'store'])->name('terceros.store');
    Route::get('/terceros/{id}/edit', [TerceroController::class, 'edit'])->name('terceros.edit');
    Route::put('/terceros/{id}', [TerceroController::class, 'update'])->name('terceros.update');
    Route::delete('/terceros/{id}', [TerceroController::class, 'destroy'])->name('terceros.destroy');
    Route::post('/terceros/{id}/update-inline', [TerceroController::class, 'updateInline'])->name('terceros.updateInline');

    // ========================================
    // DIAN (Admin Programa, Tesorería, Administrador)
    // ========================================
    Route::middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':admin_programa,tesoreria,administrador'])
        ->prefix('dian')->name('dian.')->group(function () {
        Route::get('envios', [DianController::class, 'envios'])->name('envios');
        Route::get('numeraciones', [DianController::class, 'numeraciones'])->name('numeraciones');
        Route::post('numeraciones', [DianController::class, 'storeNumeracion'])->name('numeraciones.store');
        Route::post('numeraciones/{id}/vincular', [DianController::class, 'vincularConsecutivo'])->name('numeraciones.vincular');
        Route::post('numeraciones/{id}/toggle', [DianController::class, 'toggleNumeracion'])->name('numeraciones.toggle');
        Route::delete('numeraciones/{id}', [DianController::class, 'destroyNumeracion'])->name('numeraciones.destroy');
        Route::get('configuracion', [DianController::class, 'configuracion'])->name('configuracion');
        Route::put('configuracion', [DianController::class, 'updateConfiguracion'])->name('configuracion.update');
    });

    // ========================================
    // ADMIN DEL PROGRAMA
    // ========================================
    Route::middleware(['check.role:admin_programa'])
        ->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'adminPrograma'])->name('dashboard');

        // CONSECUTIVOS
        Route::get('consecutivos/builder', [\App\Http\Controllers\ConsecutivoController::class, 'builder'])->name('consecutivos.builder');
        Route::post('consecutivos/bulk', [\App\Http\Controllers\ConsecutivoController::class, 'storeBulk'])->name('consecutivos.storeBulk');
        Route::resource('consecutivos', \App\Http\Controllers\ConsecutivoController::class);

        Route::resource('users', UserController::class)->except(['show']);
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('diagnostics/user-permissions/{user}', [\App\Http\Controllers\Admin\DiagnosticsController::class, 'userPermissions'])->name('diagnostics.user_permissions');
        Route::get('roles/users-without-role', [RolController::class, 'getUsersWithoutRole'])->name('roles.users.without');
        Route::resource('roles', RolController::class)->except(['show']);
        Route::get('roles/{role}', [RolController::class, 'show'])->name('roles.show');

        // Asignar / remover roles
        Route::post('roles/assign-role', [RolController::class, 'assignRole'])->name('roles.assign');
        Route::post('roles/remove-role', [RolController::class, 'removeRole'])->name('roles.remove');

        // Configuración y reportes
        Route::view('settings', 'admin.settings')->name('settings');
        Route::view('reports', 'admin.reports')->name('reports');

        // ========================================
        // GESTIÓN DE PERMISOS GRANULARES
        // ========================================
        Route::prefix('permisos')->name('permisos.')->group(function () {
            Route::get('/', [PermisoController::class, 'index'])->name('index');
            Route::get('crear', [PermisoController::class, 'create'])->name('create');
            Route::post('guardar', [PermisoController::class, 'store'])->name('store');
            Route::get('{id}/editar', [PermisoController::class, 'edit'])->name('edit');
            Route::put('{id}', [PermisoController::class, 'update'])->name('update');
            Route::delete('{id}', [PermisoController::class, 'destroy'])->name('destroy');
            Route::get('matriz-json', [PermisoController::class, 'matrizJson'])->name('matrizJson');
            Route::post('{roleId}/plantilla', [PermisoController::class, 'aplicarPlantilla'])->name('aplicarPlantilla');
        });
    });

    // ========================================
    // AUXILIAR
    // ========================================
    Route::middleware(['check.role:auxiliar'])
        ->prefix('auxiliar')->name('auxiliar.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'auxiliar'])->name('dashboard');
    });

    // ========================================
    // ADMINISTRADOR
    // ========================================
    Route::middleware(['check.role:administrador'])
        ->prefix('administrador')->name('administrador.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'administrador'])->name('dashboard');
    });

    // ========================================
    // TESORERIA
    // ========================================
    Route::middleware(['check.role:tesoreria'])
        ->prefix('tesoreria')->name('tesoreria.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'tesoreria'])->name('dashboard');
    });

    // ========================================
    // CONTRATOS (Administrador, Admin Programa)
    // Allow access by role OR by permission 'manage_contracts'
    Route::middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':administrador,admin_programa,manage_contracts,gestionar_contratos'])
        ->prefix('contratacion')->name('contratacion.')->group(function () {
        Route::resource('contratos', ContratoController::class);
    });

    // ========================================
    // CUENTAS DE COBRO (todos los roles del flujo)
    // ========================================
    // Allow access if user has any of these roles OR has permission 'view_cuenta_cobro'
    Route::middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':auxiliar,administrador,tesoreria,admin_programa,view_cuenta_cobro,create_cuenta_cobro'])
        ->group(function () {
        
        // Ruta de pagos (acceso también para Tesorería)
        Route::get('cuentas_cobro/pagos', [CuentaCobroController::class, 'pagos'])
            ->name('cuentas_cobro.pagos');

        // Seguimiento General (pipeline)
        Route::get('cuentas_cobro/seguimiento-general', [CuentaCobroController::class, 'seguimientoGeneral'])
            ->name('cuentas_cobro.seguimiento_general');

        // Vista de PDFs generados
        Route::get('cuentas_cobro/pdfs', [CuentaCobroController::class, 'pdfs'])
            ->name('cuentas_cobro.pdfs');

        // Movimientos General (reporte Excel)
        Route::get('cuentas_cobro/movimientos', [CuentaCobroController::class, 'movimientosGeneral'])
            ->name('cuentas_cobro.movimientos');
        
        // Exportar Movimientos a Excel
        Route::get('cuentas_cobro/movimientos/export', [CuentaCobroController::class, 'exportMovimientos'])
            ->name('cuentas_cobro.movimientos.export');

        // Seguimiento de Aprobación
        Route::get('cuentas_cobro/{id}/seguimiento', [CuentaCobroController::class, 'seguimiento'])
            ->name('cuentas_cobro.seguimiento');

        // Exportar pagos
        Route::get('cuentas_cobro/exportar-pagos', [CuentaCobroController::class, 'exportarPagos'])
            ->name('cuentas_cobro.exportar_pagos');
        
        // Generar/visualizar PDF de una cuenta de cobro
        Route::get('cuentas_cobro/{id}/pdf', [CuentaCobroController::class, 'pdf'])
            ->name('cuentas_cobro.pdf');
        
        Route::resource('cuentas_cobro', CuentaCobroController::class);
    });

    // ========================================
    // REPORTES (Administrador, Admin Programa, Tesorería)
    // Allow access by role OR by 'view_reports' permission
    Route::middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':administrador,admin_programa,tesoreria,view_reports,ver_reportes'])
        ->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReportesController::class, 'index'])->name('index');
        Route::get('/departamento/{nombre}', [ReportesController::class, 'departamento'])->name('departamento');
        Route::get('/cliente/{userId}', [ReportesController::class, 'cliente'])->name('cliente');
        Route::get('/aging', [ReportesController::class, 'aging'])->name('aging');
        Route::get('/exportar/{tipo}', [ReportesController::class, 'exportar'])->name('exportar');
    });

    // ========================================
    // INTERACCIONES (agregar notas a cuentas)
    // ========================================
    Route::middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':auxiliar,administrador,tesoreria,admin_programa,add_comments,comentar,view_cuenta_cobro'])
        ->prefix('cuentas_cobro')->name('cuentas_cobro.')->group(function () {
        Route::post('{id}/interacciones', [InteraccionController::class, 'store'])->name('interacciones.store');
        Route::delete('{id}/interacciones/{interaccionId}', [InteraccionController::class, 'destroy'])->name('interacciones.destroy');
    });

    // ========================================
    Route::prefix('cuentas_cobro')->name('cuentas_cobro.')->group(function () {
        Route::post('{cuenta}/items', [ItemCuentaCobroController::class, 'store'])->name('items.store');
        Route::delete('items/{id}', [ItemCuentaCobroController::class, 'destroy'])->name('items.destroy');
        // Soportes
        Route::post('{cuenta}/soportes', [SoporteController::class, 'store'])->name('soportes.store');
        Route::delete('{cuenta}/soportes/{soporte}', [SoporteController::class, 'destroy'])->name('soportes.destroy');
        // Archivar / Desarchivar (dueño, y con permisos)
        Route::post('{id}/archivar', [CuentaCobroController::class, 'archivar'])
            ->middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':archivar,edit_own_cuenta_cobro'])
            ->name('archivar');
        Route::post('{id}/desarchivar', [CuentaCobroController::class, 'desarchivar'])
            ->middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':archivar,edit_own_cuenta_cobro'])
            ->name('desarchivar');
        
        // ========================================
        // DEVOLUCIÓN Y ANULACIÓN DE CUENTAS
        // ========================================
        // Devolver cuenta (admin_programa, tesoreria, administrador)
        Route::post('{id}/devolver-general', [CuentaCobroController::class, 'devolverGeneral'])
            ->middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':admin_programa,tesoreria,administrador,super_admin,request_corrections'])
            ->name('devolver_general');
        
        // Anular cuenta (solo admin_programa o super_admin)
        Route::post('{id}/anular', [CuentaCobroController::class, 'anular'])
            ->middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':admin_programa,super_admin'])
            ->name('anular');
        
        // Historial completo de una cuenta
        Route::get('{id}/historial', [CuentaCobroController::class, 'historialCompleto'])
            ->name('historial');
    });

    // ========================================
    // REPORTE DE DEVOLUCIONES Y ANULACIONES
    // ========================================
    Route::middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':admin_programa,tesoreria,super_admin'])
        ->get('reportes/devoluciones', [CuentaCobroController::class, 'reporteDevoluciones'])
        ->name('reportes.devoluciones');

    // ========================================
    // APROBACIONES (Administrador, Tesorería, Admin Programa)
    // ========================================
    // ========================================
    // APROBACIONES (Administrador, Tesorería, Admin Programa)
    // Allow access by role OR by permission for approve/reject actions
    Route::middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':administrador,tesoreria,admin_programa,approve_cuenta_cobro,aprobar,reject_cuenta_cobro,rechazar'])->group(function () {
        Route::get('/aprobaciones', [CuentaCobroController::class, 'misAprobaciones'])->name('aprobaciones.index');
        Route::post('/cuentas_cobro/{id}/aprobar', [CuentaCobroController::class, 'aprobar'])->name('cuentas_cobro.aprobar');
        Route::post('/cuentas_cobro/{id}/rechazar', [CuentaCobroController::class, 'rechazar'])->name('cuentas_cobro.rechazar');
        // Devolver para corrección (Administrador)
        Route::post('/cuentas_cobro/{id}/devolver', [CuentaCobroController::class, 'devolver'])->name('cuentas_cobro.devolver');
        Route::post('/cuentas_cobro/{id}/enviar-cliente', [CuentaCobroController::class, 'enviarCliente'])->name('cuentas_cobro.enviar_cliente');
        // Acciones de pago (solo tesorería/admin_programa)
        Route::post('/cuentas_cobro/{id}/pagar', [CuentaCobroController::class, 'registrarPago'])->name('cuentas_cobro.pagar');
        Route::post('/cuentas_cobro/{id}/rechazar-pago', [CuentaCobroController::class, 'rechazarPago'])->name('cuentas_cobro.rechazar_pago');
        Route::post('/cuentas_cobro/{id}/notificar-cliente', [CuentaCobroController::class, 'notificarCliente'])->name('cuentas_cobro.notificar_cliente');
    });
    // Reenviar (dueño, y con permisos)
    Route::post('/cuentas_cobro/{id}/reenviar', [CuentaCobroController::class, 'reenviar'])
        ->middleware([\App\Http\Middleware\CheckRoleOrPermission::class . ':edit_own_cuenta_cobro,editar'])
        ->name('cuentas_cobro.reenviar');
});

