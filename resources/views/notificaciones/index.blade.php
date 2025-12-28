@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<style>
    /* Enterprise Design System - Consistent with Dashboard */
    :root {
        --primary: #116dff;
        --primary-dark: #0056d6;
        --secondary: #0f172a;
        --text-main: #334155;
        --text-light: #64748b;
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --radius-lg: 16px;
        --radius-md: 12px;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .notif-page-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 40px 24px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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
        flex-wrap: wrap;
    }

    /* Buttons */
    .btn-notif {
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

    .btn-notif-white {
        background: white;
        border-color: var(--border-color);
        color: var(--text-main);
        box-shadow: var(--shadow-sm);
    }

    .btn-notif-white:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: var(--secondary);
        transform: translateY(-1px);
        text-decoration: none;
    }

    .btn-notif-primary {
        background: var(--primary);
        color: white;
        box-shadow: 0 2px 4px rgba(17, 109, 255, 0.2);
    }

    .btn-notif-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        color: white;
        text-decoration: none;
    }

    .btn-icon-notif {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: var(--text-light);
        border: 1px solid var(--border-color);
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-icon-notif:hover {
        color: var(--primary);
        background: #eff6ff;
        border-color: #bae6fd;
    }

    /* Stats Cards Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 900px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 500px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: white;
        border-radius: var(--radius-md);
        padding: 20px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-card.stat-primary { border-left: 4px solid var(--primary); }
    .stat-card.stat-warning { border-left: 4px solid var(--warning); }
    .stat-card.stat-success { border-left: 4px solid var(--success); }
    .stat-card.stat-danger { border-left: 4px solid var(--danger); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon.icon-primary { background: #eff6ff; color: var(--primary); }
    .stat-icon.icon-warning { background: #fef3c7; color: var(--warning); }
    .stat-icon.icon-success { background: #d1fae5; color: var(--success); }
    .stat-icon.icon-danger { background: #fee2e2; color: var(--danger); }

    .stat-content h3 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        color: var(--secondary);
        line-height: 1;
    }

    .stat-content p {
        font-size: 13px;
        color: var(--text-light);
        margin: 4px 0 0 0;
        font-weight: 500;
    }

    /* Unread Banner */
    .unread-banner {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: var(--radius-md);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        color: #1e40af;
    }

    .unread-banner-icon {
        width: 40px;
        height: 40px;
        background: #3b82f6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .unread-banner p {
        margin: 0;
        font-weight: 500;
        font-size: 15px;
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
        background: linear-gradient(135deg, #fafbff 0%, #f5f8ff 100%);
        border-left: 4px solid var(--primary);
    }

    .notif-card.unread .notif-title {
        color: var(--primary-dark);
    }

    /* Icons */
    .notif-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-aprobacion { background: #ecfdf5; color: #059669; }
    .icon-rechazo { background: #fef2f2; color: #dc2626; }
    .icon-cuenta { background: #eff6ff; color: #2563eb; }
    .icon-recordatorio { background: #fef3c7; color: #d97706; }
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
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .notif-message {
        color: var(--text-main);
        font-size: 15px;
        line-height: 1.6;
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
        position: relative;
        z-index: 2;
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
        text-decoration: none;
    }

    .action-link.primary {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .action-link.primary:hover {
        background: var(--primary-dark);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: var(--radius-md);
        border: 2px dashed var(--border-color);
    }

    .empty-icon {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--secondary);
        margin: 0 0 8px 0;
    }

    .empty-text {
        color: var(--text-light);
        font-size: 15px;
        margin: 0;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .filter-row {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 150px;
    }

    .filter-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .filter-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        color: var(--text-main);
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(17, 109, 255, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .notif-card {
            flex-direction: column;
            padding: 20px;
        }
        .notif-header {
            flex-direction: column;
            gap: 12px;
        }
        .notif-actions {
            width: 100%;
            margin-top: 16px;
        }
        .action-link {
            flex: 1;
            justify-content: center;
        }
        .header-actions {
            width: 100%;
        }
        .header-actions .btn-notif {
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
                <form action="{{ route('notificaciones.marcarTodasLeidas') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-notif btn-notif-white">
                        <span class="material-symbols-rounded">done_all</span>
                        Marcar todas leídas
                    </button>
                </form>
            @endif
            <a href="{{ route('notificaciones.preferencias') }}" class="btn-notif btn-notif-white">
                <span class="material-symbols-rounded">settings</span>
                Preferencias
            </a>
            <a href="{{ route('dashboard') }}" class="btn-notif btn-notif-white">
                <span class="material-symbols-rounded">arrow_back</span>
                Volver
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    @if(isset($estadisticas))
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon icon-primary">
                <span class="material-symbols-rounded">notifications</span>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['total'] }}</h3>
                <p>Total</p>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon icon-warning">
                <span class="material-symbols-rounded">mark_email_unread</span>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['no_leidas'] }}</h3>
                <p>Sin leer</p>
            </div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon icon-success">
                <span class="material-symbols-rounded">today</span>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['hoy'] }}</h3>
                <p>Hoy</p>
            </div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon icon-danger">
                <span class="material-symbols-rounded">priority_high</span>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['accion_pendiente'] ?? 0 }}</h3>
                <p>Requiere acción</p>
            </div>
        </div>
    </div>
    @endif
    
    @if($noLeidas > 0)
        <div class="unread-banner">
            <div class="unread-banner-icon">
                <span class="material-symbols-rounded">notifications_active</span>
            </div>
            <p>Tienes <strong>{{ $noLeidas }}</strong> notificación{{ $noLeidas > 1 ? 'es' : '' }} sin leer</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('notificaciones.index') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Estado</label>
                    <select name="leida" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <option value="no" {{ request('leida') === 'no' ? 'selected' : '' }}>Sin leer</option>
                        <option value="si" {{ request('leida') === 'si' ? 'selected' : '' }}>Leídas</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Categoría</label>
                    <select name="categoria" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <option value="cuenta_cobro" {{ request('categoria') === 'cuenta_cobro' ? 'selected' : '' }}>Cuentas de Cobro</option>
                        <option value="aprobacion" {{ request('categoria') === 'aprobacion' ? 'selected' : '' }}>Aprobaciones</option>
                        <option value="recordatorio" {{ request('categoria') === 'recordatorio' ? 'selected' : '' }}>Recordatorios</option>
                        <option value="sistema" {{ request('categoria') === 'sistema' ? 'selected' : '' }}>Sistema</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Prioridad</label>
                    <select name="prioridad" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <option value="alta" {{ request('prioridad') === 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="normal" {{ request('prioridad') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="baja" {{ request('prioridad') === 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                </div>
                @if(request()->anyFilled(['leida', 'categoria', 'prioridad']))
                <div class="filter-group" style="flex: 0;">
                    <label>&nbsp;</label>
                    <a href="{{ route('notificaciones.index') }}" class="btn-notif btn-notif-white" style="padding: 10px 16px;">
                        <span class="material-symbols-rounded" style="font-size: 18px;">close</span>
                        Limpiar
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Notifications List -->
    <div class="notif-list">
        @forelse($notificaciones as $notif)
            <div class="notif-card {{ !$notif->leida ? 'unread' : '' }}">
                <!-- Icon -->
                <div class="notif-icon-box {{ 
                    match($notif->tipo ?? 'info') {
                        'aprobacion' => 'icon-aprobacion',
                        'rechazo' => 'icon-rechazo',
                        'cuenta_cobro' => 'icon-cuenta',
                        'recordatorio' => 'icon-recordatorio',
                        default => 'icon-info'
                    }
                }}">
                    <span class="material-symbols-rounded" style="font-size: 26px;">
                        {{ 
                            match($notif->tipo ?? 'info') {
                                'aprobacion' => 'check_circle',
                                'rechazo' => 'cancel',
                                'cuenta_cobro' => 'receipt_long',
                                'recordatorio' => 'alarm',
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
                        
                        <!-- Actions -->
                        <div class="notif-actions">
                            @if($notif->cuenta_cobro_id || $notif->accion_url)
                                <a href="{{ route('notificaciones.visitar', $notif->id) }}" class="action-link primary">
                                    <span class="material-symbols-rounded" style="font-size: 18px;">visibility</span>
                                    Ver detalles
                                </a>
                            @endif
                            
                            @if(!$notif->leida)
                                <form action="{{ route('notificaciones.marcarLeida', $notif->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-icon-notif" title="Marcar como leída">
                                        <span class="material-symbols-rounded" style="font-size: 20px;">check</span>
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('notificaciones.destroy', $notif->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta notificación?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-notif" title="Eliminar notificación" style="color: #ef4444;">
                                    <span class="material-symbols-rounded" style="font-size: 20px;">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="notif-message">{{ $notif->mensaje }}</p>

                    <div class="notif-footer">
                        <div class="footer-item">
                            <span class="material-symbols-rounded" style="font-size: 16px;">schedule</span>
                            {{ $notif->created_at->diffForHumans() }}
                        </div>
                        @if($notif->prioridad && $notif->prioridad !== 'normal')
                            <div class="footer-item" style="color: {{ $notif->prioridad === 'alta' ? '#ef4444' : '#64748b' }};">
                                <span class="material-symbols-rounded" style="font-size: 16px;">flag</span>
                                Prioridad {{ ucfirst($notif->prioridad) }}
                            </div>
                        @endif
                        @if($notif->leida && $notif->fecha_leida)
                            <div class="footer-item" style="color: #10b981;">
                                <span class="material-symbols-rounded" style="font-size: 16px;">done_all</span>
                                Leída {{ $notif->fecha_leida->diffForHumans() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <span class="material-symbols-rounded empty-icon">notifications_off</span>
                <h3 class="empty-title">No tienes notificaciones</h3>
                <p class="empty-text">Te avisaremos cuando haya actualizaciones importantes.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notificaciones->hasPages())
        <div style="margin-top: 32px; display: flex; justify-content: center;">
            {{ $notificaciones->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
