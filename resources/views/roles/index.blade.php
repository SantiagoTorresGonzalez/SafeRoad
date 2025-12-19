@extends('layouts.app')

@section('title', 'Gestión de Roles')

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
        --warning-amber: #f59e0b;
        --purple-primary: #6366f1;
    }

    /* Header Section */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--slate-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-subtitle {
        color: var(--slate-500);
        font-size: 0.95rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid var(--slate-200);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--slate-900);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--slate-500);
        font-weight: 500;
    }

    /* Tools Bar */
    .tools-bar {
        background: white;
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid var(--slate-200);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 300px;
    }

    .search-input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border: 1px solid var(--slate-300);
        border-radius: 8px;
        font-size: 0.95rem;
        color: var(--slate-700);
        transition: border-color 0.2s;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(17, 109, 255, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--slate-400);
        font-size: 1.25rem;
    }

    /* Roles Grid */
    .roles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
    }

    .role-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--slate-200);
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .role-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-blue);
    }

    .role-header {
        padding: 1.5rem;
        background: var(--slate-50);
        border-bottom: 1px solid var(--slate-100);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .role-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        margin-bottom: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .role-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--slate-900);
        margin-bottom: 0.25rem;
    }

    .role-meta {
        font-size: 0.875rem;
        color: var(--slate-500);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .role-body {
        padding: 1.5rem;
        flex: 1;
    }

    .permission-preview {
        margin-top: 1rem;
    }

    .permission-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background: var(--slate-100);
        color: var(--slate-600);
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .role-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--slate-100);
        display: flex;
        gap: 0.75rem;
        background: white;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid transparent;
    }

    .btn-primary {
        background: var(--primary-blue);
        color: white;
    }
    .btn-primary:hover { background: var(--primary-hover); }

    .btn-outline {
        background: white;
        border-color: var(--slate-300);
        color: var(--slate-700);
    }
    .btn-outline:hover {
        border-color: var(--slate-400);
        background: var(--slate-50);
        color: var(--slate-900);
    }

    .btn-icon {
        padding: 0.5rem;
        border-radius: 8px;
        color: var(--slate-500);
        transition: all 0.2s;
    }
    .btn-icon:hover {
        background: var(--slate-100);
        color: var(--primary-blue);
    }

    .badge-count {
        background: var(--slate-200);
        color: var(--slate-700);
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* Custom Gradients for Roles */
    .bg-gradient-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .bg-gradient-green { background: linear-gradient(135deg, #10b981, #059669); }
    .bg-gradient-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .bg-gradient-teal { background: linear-gradient(135deg, #14b8a6, #0d9488); }
    .bg-gradient-slate { background: linear-gradient(135deg, #64748b, #475569); }

</style>

<div class="container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-wrapper">
            <h1 class="page-title">
                <span class="material-symbols-rounded" style="color: var(--primary-blue);">security</span>
                Gestión de Roles
            </h1>
            <p class="page-subtitle">Administra los niveles de acceso y permisos del sistema</p>
        </div>
        <div class="actions">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <span class="material-symbols-rounded">add</span>
                Nuevo Rol
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <span class="material-symbols-rounded">badge</span>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $roles->count() }}</span>
                <span class="stat-label">Roles Totales</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #10b981;">
                <span class="material-symbols-rounded">group</span>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $roles->sum(function($role) { return $role->users->count(); }) }}</span>
                <span class="stat-label">Usuarios Asignados</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                <span class="material-symbols-rounded">verified_user</span>
            </div>
            <div class="stat-info">
                <span class="stat-value">--</span>
                <span class="stat-label">Permisos Activos</span>
            </div>
        </div>
    </div>

    <!-- Tools Bar -->
    <div class="tools-bar">
        <div class="search-box">
            <span class="material-symbols-rounded search-icon">search</span>
            <input type="text" class="search-input" placeholder="Buscar roles..." id="roleSearch">
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="roles-grid">
        @php
            $rolesTraducidos = config('permisos_traducidos.roles', []);
        @endphp
        @foreach($roles as $role)
            @php
                $rolInfo = $rolesTraducidos[$role->name] ?? null;
                $rolNombre = $rolInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $role->name));
                $rolDesc = $rolInfo['descripcion'] ?? $role->description ?? 'Sin descripción disponible para este rol.';
                $rolIcono = $rolInfo['icono'] ?? 'badge';
                $rolColor = $rolInfo['color'] ?? '#64748b';
                
                $gradientClass = match($role->name) {
                    'super_admin' => 'bg-gradient-purple',
                    'admin_programa' => 'bg-gradient-blue',
                    'administrador' => 'bg-gradient-orange',
                    'auxiliar' => 'bg-gradient-green',
                    'tesoreria' => 'bg-gradient-teal',
                    default => 'bg-gradient-slate'
                };
            @endphp
            
            <div class="role-card">
                <div class="role-header">
                    <div>
                        <div class="role-icon-wrapper" style="background: {{ $rolColor }};">
                            <span class="material-symbols-rounded">{{ $rolIcono }}</span>
                        </div>
                        <h3 class="role-name">{{ $rolNombre }}</h3>
                        <div class="role-meta">
                            <span class="material-symbols-rounded" style="font-size: 1rem;">group</span>
                            {{ $role->users->count() }} usuarios
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        @if($role->name !== 'admin_programa' && $role->name !== 'super_admin')
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este rol? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon" style="color: #ef4444; background: #fee2e2; border: none; cursor: pointer; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="Eliminar Rol">
                                    <span class="material-symbols-rounded" style="font-size: 18px;">delete</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                
                <div class="role-body">
                    <p style="color: var(--slate-500); font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.5;">
                        {{ $rolDesc }}
                    </p>
                    
                    <div class="permission-preview">
                        <span style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--slate-400); margin-bottom: 0.5rem; text-transform: uppercase;">Permisos del Rol</span>
                        @php
                            $permCount = $role->permissions?->count() ?? 0;
                        @endphp
                        @if($permCount > 0)
                            @foreach($role->permissions->take(3) as $perm)
                                @php
                                    $permInfo = config('permisos_traducidos.permisos.' . $perm->name, []);
                                    $permNombre = $permInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $perm->name));
                                @endphp
                                <span class="permission-tag">{{ $permNombre }}</span>
                            @endforeach
                            @if($permCount > 3)
                                <span class="permission-tag" style="background: var(--primary-blue); color: white;">+{{ $permCount - 3 }} más</span>
                            @endif
                        @else
                            <span class="permission-tag" style="background: #fef3c7; color: #92400e;">Sin permisos asignados</span>
                        @endif
                    </div>
                </div>

                <div class="role-footer">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline" style="flex: 1;">
                        <span class="material-symbols-rounded" style="font-size: 18px;">edit</span>
                        Editar
                    </a>
                    <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-primary" style="flex: 1;">
                        <span class="material-symbols-rounded" style="font-size: 18px;">visibility</span>
                        Ver
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    // Simple client-side search
    document.getElementById('roleSearch').addEventListener('keyup', function(e) {
        const searchText = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.role-card');
        
        cards.forEach(card => {
            const name = card.querySelector('.role-name').textContent.toLowerCase();
            if(name.includes(searchText)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endsection
