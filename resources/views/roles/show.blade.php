@extends('layouts.app')

@section('title', 'Detalles del Rol - ' . ucfirst(str_replace('_', ' ', $role->name)))

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

    .role-header {
        background: var(--purple-gradient);
        border-radius: 24px;
        padding: 40px 32px;
        color: white;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 32px;
        box-shadow: 0 10px 40px rgba(99, 102, 241, 0.2);
    }

    .role-icon-large {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
        backdrop-filter: blur(8px);
    }

    .role-header-content h1 {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .role-header-content p {
        font-size: 16px;
        opacity: 0.95;
        margin: 0;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 24px;
        transition: all 0.2s;
    }

    .back-link:hover {
        gap: 12px;
        opacity: 0.8;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    .card {
        background: white;
        border-radius: 18px;
        padding: 28px 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--slate-200);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--slate-100);
    }

    .card-header .material-symbols-rounded {
        color: var(--primary-blue);
        font-size: 28px;
    }

    .card-header h2 {
        font-size: 20px;
        font-weight: 600;
        color: var(--slate-800);
        margin: 0;
    }

    .info-group {
        margin-bottom: 20px;
    }

    .info-group:last-child {
        margin-bottom: 0;
    }

    .info-label {
        font-size: 12px;
        color: var(--slate-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .info-value {
        font-size: 16px;
        color: var(--slate-900);
        font-weight: 500;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--slate-100);
    }

    .stat-item {
        text-align: center;
        background: var(--slate-50);
        padding: 12px;
        border-radius: 10px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-blue);
        display: block;
    }

    .stat-label {
        font-size: 12px;
        color: var(--slate-500);
        margin-top: 4px;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .permission-badge {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
        color: var(--primary-blue);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        text-align: center;
    }

    .permission-badge .material-symbols-rounded {
        font-size: 18px;
    }

    .permissions-category {
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .category-title {
        font-weight: 600;
        color: var(--slate-800);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--slate-200);
    }

    .category-title .material-symbols-rounded {
        color: var(--primary-blue);
        font-size: 20px;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table thead {
        border-bottom: 2px solid var(--slate-200);
    }

    .users-table th {
        font-size: 12px;
        font-weight: 600;
        color: var(--slate-500);
        text-align: left;
        padding: 12px 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .users-table td {
        padding: 16px;
        border-bottom: 1px solid var(--slate-100);
        color: var(--slate-700);
    }

    .users-table tbody tr:hover {
        background: var(--slate-50);
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--slate-200);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-600);
        font-weight: 600;
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--slate-100);
    }

    .btn-submit {
        padding: 12px 28px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary-blue);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-secondary {
        background: white;
        border: 1px solid var(--slate-300);
        color: var(--slate-700);
    }

    .btn-secondary:hover {
        background: var(--slate-50);
        border-color: var(--slate-400);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--slate-400);
    }

    .empty-state .material-symbols-rounded {
        font-size: 48px;
        opacity: 0.5;
        display: block;
        margin-bottom: 12px;
    }

    .empty-state p {
        margin: 0;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .role-header {
            flex-direction: column;
            text-align: center;
            padding: 32px 24px;
        }

        .role-icon-large {
            display: none;
        }

        .stat-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-submit {
            width: 100%;
            justify-content: center;
        }

        .users-table {
            font-size: 12px;
        }

        .users-table th,
        .users-table td {
            padding: 8px 12px;
        }
    }
</style>

<div class="container">
    @php
        $rolesTraducidos = config('permisos_traducidos.roles', []);
        $permisosTraducidos = config('permisos_traducidos.permisos', []);
        $categoriasTraducidas = config('permisos_traducidos.categorias', []);
        
        $rolInfo = $rolesTraducidos[$role->name] ?? null;
        $rolNombre = $rolInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $role->name));
        $rolDesc = $rolInfo['descripcion'] ?? 'Gestión de permisos y usuarios del sistema';
        $rolIcono = $rolInfo['icono'] ?? 'badge';
    @endphp
    
    <a href="{{ route('admin.roles.index') }}" class="back-link">
        <span class="material-symbols-rounded" style="font-size: 20px;">arrow_back</span>
        Volver a roles
    </a>

    <!-- Role Header -->
    <div class="role-header">
        <div class="role-icon-large">
            <span class="material-symbols-rounded">{{ $rolIcono }}</span>
        </div>
        <div class="role-header-content">
            <h1>{{ $rolNombre }}</h1>
            <p>{{ $rolDesc }}</p>
        </div>
    </div>

    <div class="content-grid">
        <!-- Sidebar Information -->
        <div>
            <div class="card">
                <div class="card-header">
                    <span class="material-symbols-rounded">info</span>
                    <h2>Información del Rol</h2>
                </div>

                <div class="info-group">
                    <label class="info-label">Nombre del Rol</label>
                    <div class="info-value">{{ $rolNombre }}</div>
                </div>

                <div class="stat-grid">
                    <div class="stat-item">
                        <span class="stat-value">{{ $users->total() ?? 0 }}</span>
                        <span class="stat-label">Usuarios</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $role->permissions?->count() ?? 0 }}</span>
                        <span class="stat-label">Permisos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $role->created_at->format('y') }}</span>
                        <span class="stat-label">Año</span>
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--slate-100);">
                    <div class="info-group">
                        <label class="info-label">Creado</label>
                        <div class="info-value" style="font-size: 14px;">{{ $role->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="info-group">
                        <label class="info-label">Actualizado</label>
                        <div class="info-value" style="font-size: 14px;">{{ $role->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn-submit btn-primary" style="flex: 1; justify-content: center;">
                        <span class="material-symbols-rounded" style="font-size: 20px;">edit</span>
                        Editar
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="btn-submit btn-secondary" style="flex: 1; justify-content: center;">
                        <span class="material-symbols-rounded" style="font-size: 20px;">close</span>
                        Cerrar
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div>
            <!-- Permissions Section -->
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <span class="material-symbols-rounded">security</span>
                    <h2>Permisos Asignados</h2>
                </div>

                @if($role->permissions && $role->permissions->count() > 0)
                    @php
                        // Agrupar permisos por categoría usando la configuración
                        $permisosPorCategoria = [];
                        foreach ($role->permissions as $permission) {
                            $permInfo = $permisosTraducidos[$permission->name] ?? null;
                            $categoria = $permInfo['categoria'] ?? 'otros';
                            if (!isset($permisosPorCategoria[$categoria])) {
                                $permisosPorCategoria[$categoria] = [];
                            }
                            $permisosPorCategoria[$categoria][] = [
                                'name' => $permission->name,
                                'nombre_es' => $permInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $permission->name)),
                                'icono' => $permInfo['icono'] ?? 'check_circle'
                            ];
                        }
                    @endphp

                    @foreach($permisosPorCategoria as $catKey => $permisos)
                        @php
                            $catInfo = $categoriasTraducidas[$catKey] ?? null;
                            $catNombre = $catInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $catKey));
                            $catIcono = $catInfo['icono'] ?? 'folder';
                        @endphp
                        <div class="permissions-category">
                            <div class="category-title">
                                <span class="material-symbols-rounded">{{ $catIcono }}</span>
                                {{ $catNombre }}
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                @foreach($permisos as $permiso)
                                    <div class="permission-badge">
                                        <span class="material-symbols-rounded">{{ $permiso['icono'] }}</span>
                                        {{ $permiso['nombre_es'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <span class="material-symbols-rounded">lock_open</span>
                        <p>Este rol no tiene permisos asignados</p>
                    </div>
                @endif
            </div>

            <!-- Users Section -->
            <div class="card">
                <div class="card-header">
                    <span class="material-symbols-rounded">group</span>
                    <h2>Usuarios con este Rol</h2>
                </div>

                @if($users && $users->count() > 0)
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Fecha Registro</th>
                                <th style="text-align: center;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                            <strong>{{ $user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td style="color: var(--slate-500);">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td style="text-align: center;">
                                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success-green);"></span>
                                        <span style="font-size: 12px; color: var(--slate-500); margin-left: 4px;">Activo</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    @if($users->hasPages())
                        <div style="margin-top: 20px; display: flex; justify-content: center;">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state" style="padding: 60px 20px;">
                        <span class="material-symbols-rounded" style="font-size: 64px;">group_off</span>
                        <p style="font-size: 16px; margin-top: 16px;">No hay usuarios asignados a este rol</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
