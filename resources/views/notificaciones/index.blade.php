@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<style>
    /* Enterprise Design System - Consistent with Dashboard */
    :root {
        --primary: #116dff;
        --primary-dark: #0056d6;
        --secondary: #0f172a; /* Slate 900 */
        --text-main: #334155; /* Slate 700 */
        --text-light: #64748b; /* Slate 500 */
        --bg-body: #f8fafc; /* Slate 50 */
        --bg-card: #ffffff;
        --border-color: #e2e8f0; /* Slate 200 */
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --radius-lg: 16px;
        --radius-md: 12px;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .notif-page-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Inter', sans-serif;
    }

    /* Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--secondary);
        margin: 0 0 8px 0;
        letter-spacing: -0.025em;
    }

    .page-subtitle {
        color: var(--text-light);
        font-size: 16px;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-white {
        background: white;
        border-color: var(--border-color);
        color: var(--text-main);
        box-shadow: var(--shadow-sm);
    }

    .btn-white:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: var(--secondary);
        transform: translateY(-1px);
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        box-shadow: 0 2px 4px rgba(17, 109, 255, 0.2);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: var(--text-light);
        border: 1px solid var(--border-color);
        background: white;
    }

    .btn-icon:hover {
        color: var(--primary);
        background: #f0f9ff;
        border-color: #bae6fd;
    }

    /* Stats Banner */
    .stats-banner {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: var(--radius-md);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 32px;
        color: #1e40af;
    }

    .stats-icon {
        width: 32px;
        height: 32px;
        background: #dbeafe;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e40af;
    }

    /* Notification List */
    .notif-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .notif-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 24px;
        display: flex;
        gap: 20px;
        transition: all 0.2s ease;
        position: relative;
        box-shadow: var(--shadow-sm);
    }

    .notif-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: #cbd5e1;
    }

    .notif-card.unread {
        background: #f8fafc;
        border-left: 4px solid var(--primary);
    }

    .notif-card.unread .notif-title {
        color: var(--primary-dark);
    }

    /* Icons */
    .notif-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-aprobacion { background: #ecfdf5; color: #059669; }
    .icon-rechazo { background: #fef2f2; color: #dc2626; }
    .icon-cuenta { background: #eff6ff; color: #2563eb; }
    .icon-info { background: #f1f5f9; color: #475569; }

    /* Content */
    .notif-content {
        flex: 1;
        min-width: 0;
    }

    .notif-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
        gap: 12px;
    }

    .notif-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .notif-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--secondary);
        margin: 0;
    }

    .badge-new {
        background: #eff6ff;
        color: var(--primary);
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .notif-message {
        color: var(--text-main);
        font-size: 15px;
        line-height: 1.5;
        margin: 0 0 16px 0;
    }

    .notif-footer {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 13px;
        color: var(--text-light);
    }

    .footer-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Actions */
    .notif-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .action-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        background: white;
        border: 1px solid var(--border-color);
        color: var(--text-main);
    }

    .action-link:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: #eff6ff;
    }

    .action-link.primary {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .action-link.primary:hover {
        background: var(--primary-dark);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: var(--radius-md);
        border: 2px dashed var(--border-color);
    }

    .empty-icon {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 16px;
    }

    .empty-text {
        color: var(--text-light);
        font-size: 16px;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .notif-card {
            flex-direction: column;
        }
        .notif-header {
            flex-direction: column;
            gap: 8px;
        }
        .notif-actions {
            width: 100%;
            margin-top: 16px;
            justify-content: flex-end;
        }
        .action-link {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<div class="notif-page-container">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Notificaciones</h1>
            <p class="page-subtitle">Mantente al día con la actividad de tus cuentas de cobro</p>
        </div>
        <div class="header-actions">
            @if($noLeidas > 0)
                <form action="{{ route('notificaciones.marcarTodasLeidas') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-white">
                        <span class="material-symbols-rounded">done_all</span>
                        Marcar todas leídas
                    </button>
                </form>
            @endif
            <a href="{{ route('notificaciones.preferencias') }}" class="btn btn-white">
                <span class="material-symbols-rounded">settings</span>
                Preferencias
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-white">
                <span class="material-symbols-rounded">arrow_back</span>
                Volver
            </a>
        </div>
    </div>

    <!-- Stats Banner -->
    @if(isset($estadisticas))
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary) !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <span class="material-symbols-rounded text-primary me-2">notifications</span>
                        <div>
                            <div class="h4 mb-0">{{ $estadisticas['total'] }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--warning) !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <span class="material-symbols-rounded text-warning me-2">mark_email_unread</span>
                        <div>
                            <div class="h4 mb-0">{{ $estadisticas['no_leidas'] }}</div>
                            <small class="text-muted">Sin leer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--success) !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <span class="material-symbols-rounded text-success me-2">today</span>
                        <div>
                            <div class="h4 mb-0">{{ $estadisticas['hoy'] }}</div>
                            <small class="text-muted">Hoy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--danger) !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <span class="material-symbols-rounded text-danger me-2">priority_high</span>
                        <div>
                            <div class="h4 mb-0">{{ $estadisticas['accion_pendiente'] }}</div>
                            <small class="text-muted">Requiere acción</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($noLeidas > 0)
        <div class="stats-banner">
            <div class="stats-icon">
                <span class="material-symbols-rounded">notifications_active</span>
            </div>
            <p style="margin: 0; font-weight: 500;">Tienes <strong>{{ $noLeidas }}</strong> notificaciones sin leer</p>
        </div>
    @endif

    <!-- Notifications List -->
    <div class="notif-list">
        @forelse($notificaciones as $notif)
            <div class="notif-card {{ !$notif->leida ? 'unread' : '' }}">
                <!-- Icon -->
                <div class="notif-icon-box {{ 
                    match($notif->tipo) {
                        'aprobacion' => 'icon-aprobacion',
                        'rechazo' => 'icon-rechazo',
                        'cuenta_cobro' => 'icon-cuenta',
                        default => 'icon-info'
                    }
                }}">
                    <span class="material-symbols-rounded" style="font-size: 24px;">
                        {{ 
                            match($notif->tipo) {
                                'aprobacion' => 'check_circle',
                                'rechazo' => 'cancel',
                                'cuenta_cobro' => 'receipt_long',
                                default => 'info'
                            }
                        }}
                    </span>
                </div>

                <!-- Content -->
                <div class="notif-content">
                    <div class="notif-header">
                        <div class="notif-title-row">
                            <h3 class="notif-title">{{ $notif->titulo }}</h3>
                            @if(!$notif->leida)
                                <span class="badge-new">Nueva</span>
                            @endif
                        </div>
                        
                        <!-- Desktop Actions -->
                        <div class="notif-actions" style="display: none; @media(min-width: 640px){display:flex;}">
                            @if($notif->cuenta_cobro_id)
                                <a href="{{ route('notificaciones.visitar', $notif->id) }}" class="action-link primary">
                                    <span class="material-symbols-rounded" style="font-size: 18px;">visibility</span>
                                    Ver detalles
                                </a>
                            @endif
                            
                            @if(!$notif->leida)
                                <form action="{{ route('notificaciones.marcarLeida', $notif->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-icon" title="Marcar como leída">
                                        <span class="material-symbols-rounded" style="font-size: 20px;">check</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <p class="notif-message">{{ $notif->mensaje }}</p>

                    <div class="notif-footer">
                        <div class="footer-item">
                            <span class="material-symbols-rounded" style="font-size: 16px;">schedule</span>
                            {{ $notif->created_at->diffForHumans() }}
                        </div>
                        @if($notif->leida && $notif->fecha_leida)
                            <div class="footer-item" style="color: #94a3b8;">
                                <span class="material-symbols-rounded" style="font-size: 16px;">done_all</span>
                                Leída {{ $notif->fecha_leida->diffForHumans() }}
                            </div>
                        @endif
                    </div>

                    <!-- Mobile Actions -->
                    <div class="notif-actions" style="display: flex; margin-top: 16px; @media(min-width: 640px){display:none;}">
                        @if($notif->cuenta_cobro_id)
                            <a href="{{ route('notificaciones.visitar', $notif->id) }}" class="action-link primary" style="flex: 1; justify-content: center;">
                                Ver detalles
                            </a>
                        @endif
                        @if(!$notif->leida)
                            <form action="{{ route('notificaciones.marcarLeida', $notif->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-icon" style="width: 40px; height: 40px;">
                                    <span class="material-symbols-rounded">check</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                
                <!-- Clickable Overlay for UX -->
                @if($notif->cuenta_cobro_id)
                    <a href="{{ route('notificaciones.visitar', $notif->id) }}" style="position: absolute; inset: 0; z-index: 1;" aria-hidden="true"></a>
                    <style>
                        .notif-actions, form, button, a.action-link { position: relative; z-index: 2; }
                    </style>
                @endif
            </div>
        @empty
            <div class="empty-state">
                <span class="material-symbols-rounded empty-icon">notifications_off</span>
                <h3 style="font-size: 18px; font-weight: 600; color: var(--secondary); margin-bottom: 8px;">No tienes notificaciones</h3>
                <p class="empty-text">Te avisaremos cuando haya actualizaciones importantes.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notificaciones->hasPages())
        <div style="margin-top: 32px;">
            {{ $notificaciones->links() }}
        </div>
    @endif
</div>
@endsection
