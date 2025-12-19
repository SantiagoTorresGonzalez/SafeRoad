@extends('layouts.app')

@section('title', 'Detalle de Usuario - ' . $user->name)

@section('content')
<style>
    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --primary-blue: #116dff;
        --primary-hover: #0056d6;
        --danger-red: #ef4444;
        --success-green: #10b981;
        --purple-gradient: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
        transition: opacity 0.2s;
    }

    .back-link:hover {
        opacity: 0.8;
    }

    .profile-header-card {
        background: var(--purple-gradient);
        border-radius: 24px;
        padding: 2.5rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.2), 0 4px 6px -2px rgba(99, 102, 241, 0.1);
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 600;
        border: 4px solid rgba(255, 255, 255, 0.3);
    }

    .profile-info h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        line-height: 1.2;
    }

    .profile-email {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    .profile-badges {
        display: flex;
        gap: 0.75rem;
    }

    .header-badge {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .header-badge.role {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        color: white;
    }

    .header-badge.status {
        background: #22c55e; /* Green */
        color: white;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    .card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--slate-100);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--slate-100);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--slate-800);
        margin: 0;
    }

    .card-icon {
        color: var(--primary-blue);
        font-size: 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .info-row {
        margin-bottom: 1.5rem;
    }

    .info-row:last-child {
        margin-bottom: 0;
    }

    .info-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--slate-400);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1rem;
        color: var(--slate-800);
        font-weight: 500;
    }

    .status-pill {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-pill.active {
        background: #dcfce7;
        color: #15803d;
    }

    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        .profile-header-card {
            flex-direction: column;
            text-align: center;
            padding: 2rem;
        }
        .profile-badges {
            justify-content: center;
        }
    }
</style>

<div class="container">
    <a href="{{ route('admin.users.index') }}" class="back-link">
        <span class="material-symbols-rounded">arrow_back</span>
        Volver a usuarios
    </a>

    @php
        $rolesTraducidos = config('permisos_traducidos.roles', []);
        $permisosTraducidos = config('permisos_traducidos.permisos', []);
        $rolInfo = $rolesTraducidos[$user->role?->name ?? ''] ?? null;
        $rolNombreEs = $rolInfo['nombre_es'] ?? ($user->role ? ucfirst(str_replace('_', ' ', $user->role->name)) : 'Sin rol');
    @endphp

    <div class="profile-header-card">
        <div class="profile-avatar">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div class="profile-info">
            <h1>{{ $user->name }}</h1>
            <div class="profile-email">{{ $user->email }}</div>
            <div class="profile-badges">
                <span class="header-badge role">{{ $rolNombreEs }}</span>
                <span class="header-badge status">ACTIVO</span>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Left Column -->
        <div class="main-column">
            <!-- Personal Info -->
            <div class="card">
                <div class="card-header">
                    <span class="material-symbols-rounded card-icon">person</span>
                    <h3 class="card-title">Información Personal</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Nombre Completo</span>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha de Registro</span>
                        <div class="info-value">{{ $user->created_at->format('d/m/Y \a \l\a\s H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Última Actualización</span>
                        <div class="info-value">{{ $user->updated_at->format('d/m/Y \a \l\a\s H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Role & Permissions -->
            <div class="card">
                <div class="card-header">
                    <span class="material-symbols-rounded card-icon">verified_user</span>
                    <h3 class="card-title">Rol y Permisos</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Rol Asignado</span>
                        <div class="info-value">
                            <span class="status-pill" style="background: #e0e7ff; color: #4338ca;">
                                {{ strtoupper($rolNombreEs) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Permisos</span>
                        <div class="info-value">
                            @if($user->role && $user->role->permissions && $user->role->permissions->count() > 0)
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    @foreach($user->role->permissions as $perm)
                                        @php
                                            $permInfo = $permisosTraducidos[$perm->name] ?? null;
                                            $permNombreEs = $permInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $perm->name));
                                        @endphp
                                        <span class="status-pill" style="background: #eef2ff; color: #4338ca; font-size: .8rem; padding: 0.25rem 0.5rem; margin: 2px;">
                                            {{ $permNombreEs }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <div class="info-value text-muted">
                                    <em>Este rol no tiene permisos asignados.</em>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card">
                <div class="card-header">
                    <span class="material-symbols-rounded card-icon">manage_accounts</span>
                    <h3 class="card-title">Información de Cuenta</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Estado</span>
                        <div class="info-value">
                            <span class="status-pill active">ACTIVO</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo de Usuario</span>
                        <div class="info-value">Interno</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="side-column">
            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">ID de Usuario</span>
                        <div class="info-value">{{ $user->id }}</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Estado</span>
                        <div class="info-value">
                            <span class="status-pill active" style="font-size: 0.75rem;">ACTIVO</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Permisos</span>
                        <div class="info-value">{{ $user->role ? $user->role->permissions->count() : 0 }} permisos</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actividad Reciente</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Creado</span>
                        <div class="info-value" style="font-size: 0.9rem;">{{ $user->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Actualizado</span>
                        <div class="info-value" style="font-size: 0.9rem;">{{ $user->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Seguridad</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Contraseña</span>
                        <div class="info-value">Configurada</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Verificación</span>
                        <div class="info-value">
                            <span class="status-pill active" style="font-size: 0.75rem;">VERIFICADO</span>
                        </div>
                    </div>
                    <div class="mt-3">
                         <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary w-100" style="justify-content: center;">
                            Editar Usuario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
