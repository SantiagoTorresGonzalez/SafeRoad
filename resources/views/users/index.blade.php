@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<link rel="stylesheet" href="{{ asset('css/views/users.css') }}">

<div class="users-container">
    <!-- Header con Gradiente -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <span class="material-symbols-rounded">group</span>
            </div>
            <div class="header-text">
                <h1>Gestión de Usuarios</h1>
                <p>Administra los usuarios del sistema y asigna roles</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.roles.index') }}" class="btn-header">
                <span class="material-symbols-rounded">admin_panel_settings</span>
                Ver Roles
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn-header primary">
                <span class="material-symbols-rounded">person_add</span>
                Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="icon-box blue">
                <span class="material-symbols-rounded">group</span>
            </div>
            <div class="info">
                <h4>{{ $users->count() }}</h4>
                <p>Total Usuarios</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon-box green">
                <span class="material-symbols-rounded">verified_user</span>
            </div>
            <div class="info">
                <h4>{{ $users->whereNotNull('role_id')->count() }}</h4>
                <p>Con Rol Asignado</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon-box orange">
                <span class="material-symbols-rounded">person_off</span>
            </div>
            <div class="info">
                <h4>{{ $users->whereNull('role_id')->count() }}</h4>
                <p>Sin Rol</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon-box purple">
                <span class="material-symbols-rounded">shield_person</span>
            </div>
            <div class="info">
                <h4>{{ $users->filter(fn($u) => $u->role && in_array($u->role->name, ['super_admin', 'admin_programa']))->count() }}</h4>
                <p>Administradores</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    @php
        $rolesTraducidos = config('permisos_traducidos.roles', []);
    @endphp
    <div class="filters-bar">
        <div class="filter-group" style="flex: 2;">
            <label>Buscar</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Nombre, email..." onkeyup="filterTable()">
        </div>
        <div class="filter-group">
            <label>Rol</label>
            <select id="roleFilter" class="form-control" onchange="filterTable()">
                <option value="">Todos los roles</option>
                @foreach($rolesTraducidos as $roleName => $rolInfo)
                    <option value="{{ $roleName }}">{{ $rolInfo['nombre_es'] }}</option>
                @endforeach
                <option value="sin_rol">Sin Rol</option>
            </select>
        </div>
    </div>

    <!-- Tabla -->
    <div class="excel-wrapper">
        <div class="excel-scroll">
            @if($users->count() > 0)
            <table class="excel-table" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Último Acceso</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                        @php
                            $roleName = $user->role ? strtolower($user->role->name) : 'sin_rol';
                            $rolInfo = $rolesTraducidos[$user->role?->name ?? ''] ?? null;
                            $rolNombreEs = $rolInfo['nombre_es'] ?? ($user->role ? ucfirst(str_replace('_', ' ', $user->role->name)) : 'Sin Rol');
                            $badgeClass = match($roleName) {
                                'super_admin' => 'badge-red',
                                'admin_programa' => 'badge-purple',
                                'administrador' => 'badge-blue',
                                'auxiliar' => 'badge-green',
                                'tesoreria' => 'badge-pink',
                                default => 'badge-gray'
                            };
                        @endphp
                        <tr data-role="{{ $roleName }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    <div class="user-info">
                                        <span class="name">{{ $user->name }}</span>
                                        <span class="email">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $rolNombreEs }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}<br><span class="text-muted">{{ $user->created_at->format('H:i') }}</span></td>
                            <td>{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.users.show', $user) }}" class="action-btn view" title="Ver">
                                        <span class="material-symbols-rounded">visibility</span>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="action-btn edit" title="Editar">
                                        <span class="material-symbols-rounded">edit</span>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Eliminar este usuario?')" class="action-btn delete" title="Eliminar">
                                            <span class="material-symbols-rounded">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <span class="material-symbols-rounded icon">group_off</span>
                <h3>No hay usuarios registrados</h3>
                <p>Comienza agregando el primer usuario al sistema</p>
                <a href="{{ route('admin.users.create') }}" class="btn-header primary" style="background: #00b5e2; color: white; display: inline-flex;">
                    <span class="material-symbols-rounded">person_add</span>
                    Crear Usuario
                </a>
            </div>
            @endif
        </div>
        @if($users->count() > 0)
        <div class="pagination-wrapper">
            <span class="pagination-info">Mostrando {{ $users->count() }} usuarios</span>
        </div>
        @endif
    </div>
</div>

<script>
function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toUpperCase();
    const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
    const table = document.getElementById('usersTable');
    
    if (!table) return;
    
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const role = row.getAttribute('data-role');
        const cells = row.getElementsByTagName('td');
        let showRow = true;

        // Filter by role
        if (roleFilter && role !== roleFilter) {
            showRow = false;
        }

        // Filter by search text
        if (searchInput && showRow) {
            let found = false;
            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toUpperCase().indexOf(searchInput) > -1) {
                    found = true;
                    break;
                }
            }
            if (!found) showRow = false;
        }

        row.style.display = showRow ? '' : 'none';
    }
}
</script>
@endsection
