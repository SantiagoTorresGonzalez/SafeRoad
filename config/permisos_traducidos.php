<?php

/**
 * Traducciones y descripciones de permisos en español
 * Este archivo mapea los nombres de permisos en inglés a español
 * con descripciones detalladas para cada permiso.
 * 
 * Los permisos granulares (PermisoGranular) son configuraciones avanzadas
 * que definen qué acciones puede realizar cada rol en cada etapa del flujo
 * de aprobación de cuentas de cobro.
 */

return [
    // ========================================
    // CATEGORÍAS DE PERMISOS
    // ========================================
    'categorias' => [
        'cuentas_cobro' => [
            'nombre' => 'Cuentas de Cobro',
            'icono' => 'receipt_long',
            'descripcion' => 'Gestión de cuentas de cobro y su flujo de aprobación',
        ],
        'documentos' => [
            'nombre' => 'Documentos y Soportes',
            'icono' => 'description',
            'descripcion' => 'Gestión de documentos adjuntos y soportes',
        ],
        'contratos' => [
            'nombre' => 'Contratos',
            'icono' => 'handshake',
            'descripcion' => 'Administración de contratos y terceros',
        ],
        'pagos' => [
            'nombre' => 'Pagos y Tesorería',
            'icono' => 'payments',
            'descripcion' => 'Procesamiento de pagos y transacciones',
        ],
        'presupuesto' => [
            'nombre' => 'Presupuesto',
            'icono' => 'trending_up',
            'descripcion' => 'Gestión y visualización de presupuesto',
        ],
        'reportes' => [
            'nombre' => 'Reportes',
            'icono' => 'bar_chart',
            'descripcion' => 'Generación y visualización de reportes',
        ],
        'administracion' => [
            'nombre' => 'Administración',
            'icono' => 'admin_panel_settings',
            'descripcion' => 'Gestión de usuarios, roles y sistema',
        ],
        'terceros' => [
            'nombre' => 'Terceros',
            'icono' => 'group',
            'descripcion' => 'Gestión de clientes, proveedores y contratistas',
        ],
        'notificaciones' => [
            'nombre' => 'Notificaciones',
            'icono' => 'notifications',
            'descripcion' => 'Gestión de alertas y comunicaciones',
        ],
    ],

    // ========================================
    // PERMISOS TRADUCIDOS
    // ========================================
    'permisos' => [
        // --- Cuentas de Cobro ---
        'create_cuenta_cobro' => [
            'nombre_es' => 'Crear Cuenta de Cobro',
            'descripcion' => 'Permite crear nuevas cuentas de cobro',
            'categoria' => 'cuentas_cobro',
            'icono' => 'add_circle',
        ],
        'view_cuenta_cobro' => [
            'nombre_es' => 'Ver Cuentas de Cobro',
            'descripcion' => 'Permite visualizar cuentas de cobro',
            'categoria' => 'cuentas_cobro',
            'icono' => 'visibility',
        ],
        'view_own_cuenta_cobro' => [
            'nombre_es' => 'Ver Cuentas Propias',
            'descripcion' => 'Solo puede ver las cuentas que ha creado',
            'categoria' => 'cuentas_cobro',
            'icono' => 'person',
        ],
        'view_all_cuenta_cobro' => [
            'nombre_es' => 'Ver Todas las Cuentas',
            'descripcion' => 'Puede ver cuentas de todos los usuarios',
            'categoria' => 'cuentas_cobro',
            'icono' => 'groups',
        ],
        'edit_own_cuenta_cobro' => [
            'nombre_es' => 'Editar Cuentas Propias',
            'descripcion' => 'Puede editar las cuentas que ha creado',
            'categoria' => 'cuentas_cobro',
            'icono' => 'edit',
        ],
        'review_cuenta_cobro' => [
            'nombre_es' => 'Revisar Cuentas',
            'descripcion' => 'Puede revisar y validar cuentas de cobro',
            'categoria' => 'cuentas_cobro',
            'icono' => 'fact_check',
        ],
        'approve_cuenta_cobro' => [
            'nombre_es' => 'Aprobar Cuentas',
            'descripcion' => 'Puede aprobar cuentas de cobro',
            'categoria' => 'cuentas_cobro',
            'icono' => 'check_circle',
        ],
        'reject_cuenta_cobro' => [
            'nombre_es' => 'Rechazar Cuentas',
            'descripcion' => 'Puede rechazar cuentas de cobro',
            'categoria' => 'cuentas_cobro',
            'icono' => 'cancel',
        ],
        'final_approval' => [
            'nombre_es' => 'Aprobación Final',
            'descripcion' => 'Puede dar la aprobación final de cuentas',
            'categoria' => 'cuentas_cobro',
            'icono' => 'verified',
        ],

        // --- Documentos ---
        'upload_documents' => [
            'nombre_es' => 'Subir Documentos',
            'descripcion' => 'Puede subir documentos y soportes',
            'categoria' => 'documentos',
            'icono' => 'upload_file',
        ],
        'view_documents' => [
            'nombre_es' => 'Ver Documentos',
            'descripcion' => 'Puede visualizar documentos adjuntos',
            'categoria' => 'documentos',
            'icono' => 'folder_open',
        ],
        'delete_documents' => [
            'nombre_es' => 'Eliminar Documentos',
            'descripcion' => 'Puede eliminar documentos adjuntos',
            'categoria' => 'documentos',
            'icono' => 'delete',
        ],

        // --- Contratos ---
        'view_contract_info' => [
            'nombre_es' => 'Ver Información de Contratos',
            'descripcion' => 'Puede ver detalles de contratos',
            'categoria' => 'contratos',
            'icono' => 'info',
        ],
        'manage_contracts' => [
            'nombre_es' => 'Gestionar Contratos',
            'descripcion' => 'Puede crear, editar y eliminar contratos',
            'categoria' => 'contratos',
            'icono' => 'edit_document',
        ],
        'contract_validation' => [
            'nombre_es' => 'Validar Contratos',
            'descripcion' => 'Puede validar y aprobar contratos',
            'categoria' => 'contratos',
            'icono' => 'task_alt',
        ],

        // --- Pagos ---
        'authorize_payment' => [
            'nombre_es' => 'Autorizar Pago',
            'descripcion' => 'Puede autorizar pagos pendientes',
            'categoria' => 'pagos',
            'icono' => 'approval',
        ],
        'process_payment' => [
            'nombre_es' => 'Procesar Pago',
            'descripcion' => 'Puede procesar y ejecutar pagos',
            'categoria' => 'pagos',
            'icono' => 'payment',
        ],
        'generate_checks' => [
            'nombre_es' => 'Generar Cheques',
            'descripcion' => 'Puede generar cheques para pagos',
            'categoria' => 'pagos',
            'icono' => 'money',
        ],
        'bank_transfers' => [
            'nombre_es' => 'Transferencias Bancarias',
            'descripcion' => 'Puede realizar transferencias bancarias',
            'categoria' => 'pagos',
            'icono' => 'account_balance',
        ],
        'payment_confirmation' => [
            'nombre_es' => 'Confirmar Pago',
            'descripcion' => 'Puede confirmar la ejecución de pagos',
            'categoria' => 'pagos',
            'icono' => 'done_all',
        ],
        'generate_payment_orders' => [
            'nombre_es' => 'Generar Órdenes de Pago',
            'descripcion' => 'Puede generar órdenes de pago',
            'categoria' => 'pagos',
            'icono' => 'receipt',
        ],

        // --- Presupuesto ---
        'view_budget' => [
            'nombre_es' => 'Ver Presupuesto',
            'descripcion' => 'Puede visualizar información de presupuesto',
            'categoria' => 'presupuesto',
            'icono' => 'monitoring',
        ],
        'manage_budget' => [
            'nombre_es' => 'Gestionar Presupuesto',
            'descripcion' => 'Puede modificar el presupuesto',
            'categoria' => 'presupuesto',
            'icono' => 'savings',
        ],

        // --- Reportes ---
        'view_reports' => [
            'nombre_es' => 'Ver Reportes',
            'descripcion' => 'Puede ver reportes del sistema',
            'categoria' => 'reportes',
            'icono' => 'analytics',
        ],
        'financial_reports' => [
            'nombre_es' => 'Reportes Financieros',
            'descripcion' => 'Acceso a reportes financieros',
            'categoria' => 'reportes',
            'icono' => 'query_stats',
        ],
        'view_financial_reports' => [
            'nombre_es' => 'Ver Reportes Financieros',
            'descripcion' => 'Puede visualizar reportes financieros',
            'categoria' => 'reportes',
            'icono' => 'leaderboard',
        ],
        'contract_reports' => [
            'nombre_es' => 'Reportes de Contratos',
            'descripcion' => 'Acceso a reportes de contratos',
            'categoria' => 'reportes',
            'icono' => 'summarize',
        ],

        // --- Administración ---
        'manage_users' => [
            'nombre_es' => 'Gestionar Usuarios',
            'descripcion' => 'Puede crear, editar y eliminar usuarios',
            'categoria' => 'administracion',
            'icono' => 'manage_accounts',
        ],
        'manage_contractors' => [
            'nombre_es' => 'Gestionar Contratistas',
            'descripcion' => 'Puede administrar contratistas',
            'categoria' => 'administracion',
            'icono' => 'engineering',
        ],
        'contractor_registration' => [
            'nombre_es' => 'Registrar Contratistas',
            'descripcion' => 'Puede registrar nuevos contratistas',
            'categoria' => 'administracion',
            'icono' => 'person_add',
        ],
        'system_admin' => [
            'nombre_es' => 'Administración del Sistema',
            'descripcion' => 'Acceso total al sistema',
            'categoria' => 'administracion',
            'icono' => 'settings',
        ],

        // --- Otros ---
        'add_comments' => [
            'nombre_es' => 'Agregar Comentarios',
            'descripcion' => 'Puede agregar comentarios a las cuentas',
            'categoria' => 'cuentas_cobro',
            'icono' => 'comment',
        ],
        'request_corrections' => [
            'nombre_es' => 'Solicitar Correcciones',
            'descripcion' => 'Puede devolver cuentas para corrección',
            'categoria' => 'cuentas_cobro',
            'icono' => 'undo',
        ],
        'override_decisions' => [
            'nombre_es' => 'Anular Decisiones',
            'descripcion' => 'Puede anular aprobaciones/rechazos previos',
            'categoria' => 'administracion',
            'icono' => 'restart_alt',
        ],
    ],

    // ========================================
    // PERMISOS GRANULARES (para la matriz)
    // ========================================
    'granulares' => [
        'puede_crear' => [
            'nombre_es' => 'Crear',
            'descripcion' => 'Puede crear nuevos registros',
            'icono' => 'add_circle',
        ],
        'puede_leer' => [
            'nombre_es' => 'Leer',
            'descripcion' => 'Puede visualizar registros',
            'icono' => 'visibility',
        ],
        'puede_editar' => [
            'nombre_es' => 'Editar',
            'descripcion' => 'Puede modificar registros',
            'icono' => 'edit',
        ],
        'puede_eliminar' => [
            'nombre_es' => 'Eliminar',
            'descripcion' => 'Puede eliminar registros',
            'icono' => 'delete',
        ],
        'puede_aprobar' => [
            'nombre_es' => 'Aprobar',
            'descripcion' => 'Puede aprobar solicitudes',
            'icono' => 'check_circle',
        ],
        'puede_rechazar' => [
            'nombre_es' => 'Rechazar',
            'descripcion' => 'Puede rechazar solicitudes',
            'icono' => 'cancel',
        ],
        'puede_devolver' => [
            'nombre_es' => 'Devolver',
            'descripcion' => 'Puede devolver a la etapa anterior',
            'icono' => 'undo',
        ],
        'puede_devolver_correccion' => [
            'nombre_es' => 'Devolver para Corrección',
            'descripcion' => 'Puede solicitar correcciones',
            'icono' => 'assignment_return',
        ],
        'puede_comentar' => [
            'nombre_es' => 'Comentar',
            'descripcion' => 'Puede agregar comentarios',
            'icono' => 'comment',
        ],
        'puede_subir_documentos' => [
            'nombre_es' => 'Subir Documentos',
            'descripcion' => 'Puede cargar archivos',
            'icono' => 'upload_file',
        ],
        'puede_descargar_documentos' => [
            'nombre_es' => 'Descargar Documentos',
            'descripcion' => 'Puede descargar archivos',
            'icono' => 'download',
        ],
        'puede_registrar_pago' => [
            'nombre_es' => 'Registrar Pago',
            'descripcion' => 'Puede registrar pagos',
            'icono' => 'payment',
        ],
        'puede_enviar_cliente' => [
            'nombre_es' => 'Enviar a Cliente',
            'descripcion' => 'Puede enviar notificaciones al cliente',
            'icono' => 'send',
        ],
        'puede_archivar' => [
            'nombre_es' => 'Archivar',
            'descripcion' => 'Puede archivar registros',
            'icono' => 'archive',
        ],
        'puede_ver_todas_cuentas' => [
            'nombre_es' => 'Ver Todas las Cuentas',
            'descripcion' => 'Acceso a todas las cuentas',
            'icono' => 'groups',
        ],
        'puede_ver_reportes' => [
            'nombre_es' => 'Ver Reportes',
            'descripcion' => 'Acceso a reportes',
            'icono' => 'bar_chart',
        ],
        'puede_gestionar_usuarios' => [
            'nombre_es' => 'Gestionar Usuarios',
            'descripcion' => 'Administrar usuarios del sistema',
            'icono' => 'manage_accounts',
        ],
        'puede_gestionar_contratos' => [
            'nombre_es' => 'Gestionar Contratos',
            'descripcion' => 'Administrar contratos',
            'icono' => 'handshake',
        ],
    ],

    // ========================================
    // ETAPAS DEL FLUJO
    // ========================================
    'etapas_flujo' => [
        'auxiliar' => [
            'nombre_es' => 'Auxiliar',
            'descripcion' => 'Etapa inicial - Creación y gestión de cuentas',
            'orden' => 1,
            'icono' => 'support_agent',
            'color' => '#3b82f6', // Azul
        ],
        'administrador' => [
            'nombre_es' => 'Administrador',
            'descripcion' => 'Revisión y aprobación de cuentas',
            'orden' => 2,
            'icono' => 'admin_panel_settings',
            'color' => '#8b5cf6', // Violeta
        ],
        'tesoreria' => [
            'nombre_es' => 'Tesorería',
            'descripcion' => 'Procesamiento de pagos',
            'orden' => 3,
            'icono' => 'account_balance',
            'color' => '#10b981', // Verde
        ],
    ],

    // ========================================
    // ROLES DEL SISTEMA
    // ========================================
    'roles' => [
        'super_admin' => [
            'nombre_es' => 'Super Administrador',
            'descripcion' => 'Control total del sistema',
            'icono' => 'shield_person',
            'color' => '#ef4444',
        ],
        'admin_programa' => [
            'nombre_es' => 'Administrador de Programa',
            'descripcion' => 'Administración general del programa',
            'icono' => 'manage_accounts',
            'color' => '#8b5cf6',
        ],
        'administrador' => [
            'nombre_es' => 'Administrador',
            'descripcion' => 'Revisa y aprueba cuentas de cobro',
            'icono' => 'admin_panel_settings',
            'color' => '#3b82f6',
        ],
        'tesoreria' => [
            'nombre_es' => 'Tesorería',
            'descripcion' => 'Procesa pagos y gestiona finanzas',
            'icono' => 'account_balance',
            'color' => '#10b981',
        ],
        'auxiliar' => [
            'nombre_es' => 'Auxiliar',
            'descripcion' => 'Crea y gestiona cuentas de cobro',
            'icono' => 'support_agent',
            'color' => '#f59e0b',
        ],
        'contador' => [
            'nombre_es' => 'Contador',
            'descripcion' => 'Revisa aspectos contables',
            'icono' => 'calculate',
            'color' => '#06b6d4',
        ],
    ],
];
