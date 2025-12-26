@extends('layouts.app')

@section('title', 'Editar Rol - ' . ucfirst(str_replace('_', ' ', $role->name)))

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
        --purple-gradient: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%);
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
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

    .page-header {
        background: var(--purple-gradient);
        border-radius: 24px;
        padding: 40px 32px;
        color: white;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 10px 40px rgba(124, 58, 237, 0.15);
        flex-wrap: wrap;
    }

    .page-header-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .page-header-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .page-header p {
        font-size: 14px;
        opacity: 0.9;
        margin: 4px 0 0 0;
    }

    .header-stats {
        display: flex;
        gap: 24px;
    }

    .stat-box {
        text-align: center;
        background: rgba(255, 255, 255, 0.15);
        padding: 12px 24px;
        border-radius: 12px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        display: block;
    }

    .stat-label {
        font-size: 12px;
        opacity: 0.9;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
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
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
    }

    .card-header .material-symbols-rounded {
        color: var(--primary-blue);
        font-size: 28px;
    }

    .card-header h2 {
        font-size: 18px;
        font-weight: 600;
        color: var(--slate-800);
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--slate-700);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label .required {
        color: var(--danger);
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        font-size: 15px;
        font-family: inherit;
        transition: all 0.2s;
        box-sizing: border-box;
        background: var(--slate-50);
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 4px rgba(17, 109, 255, 0.1);
        background: white;
    }

    .form-input:disabled {
        background: #f5f5f5;
        color: var(--slate-400);
        cursor: not-allowed;
    }

    .info-box {
        background: linear-gradient(135deg, rgba(17, 109, 255, 0.05), rgba(99, 102, 241, 0.05));
        border-left: 4px solid var(--primary-blue);
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 14px;
        color: var(--primary-blue);
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .info-box .material-symbols-rounded {
        font-size: 20px;
        flex-shrink: 0;
        margin-top: -2px;
    }

    .alert-warning-custom {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(251, 191, 36, 0.05));
        border-left: 4px solid var(--warning);
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 14px;
        color: #b45309;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .permission-category {
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        margin-bottom: 16px;
        overflow: hidden;
    }

    .category-header {
        padding: 14px 20px;
        background: linear-gradient(135deg, var(--slate-100), var(--slate-50));
        border-bottom: 1px solid var(--slate-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .category-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 700;
        color: var(--slate-800);
    }

    .category-title .material-symbols-rounded {
        font-size: 22px;
        color: var(--primary-blue);
    }

    .category-badge {
        background: var(--primary-blue);
        color: white;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .category-body {
        padding: 16px;
    }

    .permissions-list {
        display: grid;
        gap: 8px;
    }

    .permission-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: white;
        border: 2px solid var(--slate-200);
        border-radius: 10px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .permission-item:hover {
        border-color: var(--primary-blue);
        background: rgba(17, 109, 255, 0.02);
    }

    .permission-item.selected {
        border-color: var(--primary-blue);
        background: rgba(17, 109, 255, 0.05);
    }

    .permission-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: var(--primary-blue);
        flex-shrink: 0;
    }

    .permission-content {
        flex: 1;
    }

    .permission-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--slate-800);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .permission-name .material-symbols-rounded {
        font-size: 18px;
        color: var(--slate-400);
    }

    .permission-description {
        font-size: 12px;
        color: var(--slate-500);
        margin-top: 2px;
    }

    .toolbar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .btn-tool {
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid var(--slate-200);
        background: white;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: var(--slate-600);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-tool:hover {
        background: var(--slate-50);
        border-color: var(--primary-blue);
        color: var(--primary-blue);
    }

    .btn-tool .material-symbols-rounded {
        font-size: 18px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid rgba(0, 0, 0, 0.05);
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

    .btn-primary-submit {
        background: linear-gradient(135deg, #0071e3 0%, #0056b3 100%);
        color: white;
        flex: 1;
        justify-content: center;
    }

    .btn-primary-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 113, 227, 0.3);
    }

    .btn-cancel {
        background: rgba(0, 0, 0, 0.05);
        color: var(--slate-700);
    }

    .btn-cancel:hover {
        background: rgba(0, 0, 0, 0.1);
    }

    .role-meta {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--slate-200);
    }

    .meta-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 13px;
    }

    .meta-label {
        color: var(--slate-500);
    }

    .meta-value {
        color: var(--slate-800);
        font-weight: 500;
    }
</style>

<div class="container">
    <a href="{{ route('admin.roles.index') }}" class="back-link">
        <span class="material-symbols-rounded" style="font-size: 20px;">arrow_back</span>
        Volver a roles
    </a>

    @php
        $traduccion = config('permisos_traducidos', []);
        $rolesTraducidos = $traduccion['roles'] ?? [];
        $rolInfo = $rolesTraducidos[$role->name] ?? null;
        $rolNombre = $rolInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $role->name));
        $rolColor = $rolInfo['color'] ?? '#8b5cf6';
        $rolIcono = $rolInfo['icono'] ?? 'badge';
        $isSystemRole = in_array($role->name, ['auxiliar', 'administrador', 'tesoreria', 'admin_programa', 'super_admin']);
    @endphp

    <div class="page-header" style="background: linear-gradient(135deg, {{ $rolColor }}dd 0%, {{ $rolColor }}99 100%);">
        <div class="page-header-content">
            <div class="page-header-icon">
                <span class="material-symbols-rounded">{{ $rolIcono }}</span>
            </div>
            <div>
                <h1>Editar Rol</h1>
                <p>{{ $rolNombre }}</p>
            </div>
        </div>
        <div class="header-stats">
            <div class="stat-box">
                <span class="stat-value">{{ $role->users()->count() }}</span>
                <span class="stat-label">Usuarios</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">{{ $role->permissions?->count() ?? 0 }}</span>
                <span class="stat-label">Permisos</span>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Sidebar - Información del Rol -->
        <div>
            <div class="card">
                <div class="card-header">
                    <span class="material-symbols-rounded">info</span>
                    <h2>Información del Rol</h2>
                </div>

                @if($isSystemRole)
                    <div class="alert-warning-custom">
                        <span class="material-symbols-rounded">lock</span>
                        <span>Este es un rol del sistema. Editar puede afectar el flujo de trabajo.</span>
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Nombre del Rol</label>
                    <input id="roleNameInput" name="role_name_ui" type="text" class="form-input" value="{{ old('name', $role->name) }}" {{ $isSystemRole ? 'disabled' : '' }}>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea id="roleDescriptionInput" name="role_description_ui" class="form-input" rows="3" placeholder="Describe las responsabilidades de este rol...">{{ old('description', $role->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Acción de Cumplimiento</label>
                    <input id="roleAccionInput" name="role_accion_ui" type="text" class="form-input" value="{{ old('accion_cumplimiento', $role->accion_cumplimiento) }}" placeholder="Ej: Aprobar pagos">
                    <small style="color: var(--slate-500); font-size: 12px; margin-top: 4px; display: block;">Qué acción ejecuta este rol en el flujo</small>
                </div>

                <div class="role-meta">
                    <div class="meta-item">
                        <span class="meta-label">Creado:</span>
                        <span class="meta-value">{{ $role->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Actualizado:</span>
                        <span class="meta-value">{{ $role->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Permisos -->
        <div>
            <div class="card">
                <div class="card-header">
                    <span class="material-symbols-rounded">security</span>
                    <h2>Gestionar Permisos</h2>
                </div>

                <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" id="editRoleForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="roleNameHidden" name="name" value="{{ old('name', $role->name) }}">
                    <input type="hidden" id="roleDescriptionHidden" name="description" value="{{ old('description', $role->description) }}">
                    <input type="hidden" id="roleAccionHidden" name="accion_cumplimiento" value="{{ old('accion_cumplimiento', $role->accion_cumplimiento) }}">

                    @if($role->users()->count() > 0)
                        <div class="info-box">
                            <span class="material-symbols-rounded">info</span>
                            <span>Este rol tiene <strong>{{ $role->users()->count() }} usuario(s)</strong>. Los cambios se aplicarán inmediatamente a todos.</span>
                        </div>
                    @endif

                    <div class="toolbar">
                        <button type="button" class="btn-tool" onclick="selectAllPermissions()">
                            <span class="material-symbols-rounded">check_circle</span>
                            Seleccionar Todo
                        </button>
                        <button type="button" class="btn-tool" onclick="clearAllPermissions()">
                            <span class="material-symbols-rounded">radio_button_unchecked</span>
                            Limpiar Todo
                        </button>
                    </div>

                    @php
                        $categorias = $traduccion['categorias'] ?? [];
                        $permisosTraducidos = $traduccion['permisos'] ?? [];
                        $permisosAsignados = $role->permissions->pluck('id')->toArray();

                        $permisosPorCategoria = [];
                        foreach ($availablePermissions as $permission) {
                            $info = $permisosTraducidos[$permission->name] ?? null;
                            $categoria = $info['categoria'] ?? 'otros';
                            if (!isset($permisosPorCategoria[$categoria])) {
                                $permisosPorCategoria[$categoria] = [];
                            }
                            $permisosPorCategoria[$categoria][] = [
                                'permission' => $permission,
                                'info' => $info,
                            ];
                        }
                    @endphp

                    @foreach($categorias as $catKey => $catInfo)
                        @if(isset($permisosPorCategoria[$catKey]) && count($permisosPorCategoria[$catKey]) > 0)
                            <div class="permission-category">
                                <div class="category-header">
                                    <div class="category-title">
                                        <span class="material-symbols-rounded">{{ $catInfo['icono'] ?? 'folder' }}</span>
                                        {{ $catInfo['nombre'] }}
                                    </div>
                                    <span class="category-badge">{{ count($permisosPorCategoria[$catKey]) }}</span>
                                </div>
                                <div class="category-body">
                                    <div class="permissions-list">
                                        @foreach($permisosPorCategoria[$catKey] as $item)
                                            @php
                                                $perm = $item['permission'];
                                                $info = $item['info'];
                                                $nombreEs = $info['nombre_es'] ?? ucfirst(str_replace('_', ' ', $perm->name));
                                                $descripcion = $info['descripcion'] ?? '';
                                                $icono = $info['icono'] ?? 'check';
                                                $isChecked = in_array($perm->id, $permisosAsignados);
                                            @endphp
                                            <label class="permission-item {{ $isChecked ? 'selected' : '' }}">
                                                <input type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $perm->id }}" 
                                                       class="permission-checkbox"
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                <div class="permission-content">
                                                    <div class="permission-name">
                                                        <span class="material-symbols-rounded">{{ $icono }}</span>
                                                        {{ $nombreEs }}
                                                    </div>
                                                    @if($descripcion)
                                                        <div class="permission-description">{{ $descripcion }}</div>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(isset($permisosPorCategoria['otros']) && count($permisosPorCategoria['otros']) > 0)
                        <div class="permission-category">
                            <div class="category-header">
                                <div class="category-title">
                                    <span class="material-symbols-rounded">extension</span>
                                    Otros Permisos
                                </div>
                                <span class="category-badge">{{ count($permisosPorCategoria['otros']) }}</span>
                            </div>
                            <div class="category-body">
                                <div class="permissions-list">
                                    @foreach($permisosPorCategoria['otros'] as $item)
                                        @php
                                            $perm = $item['permission'];
                                            $info = $item['info'];
                                            $nombreEs = $info['nombre_es'] ?? ucfirst(str_replace('_', ' ', $perm->name));
                                            $descripcion = $info['descripcion'] ?? '';
                                            $icono = $info['icono'] ?? 'check';
                                            $isChecked = in_array($perm->id, $permisosAsignados);
                                        @endphp
                                        <label class="permission-item {{ $isChecked ? 'selected' : '' }}">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $perm->id }}" 
                                                   class="permission-checkbox"
                                                   {{ $isChecked ? 'checked' : '' }}>
                                            <div class="permission-content">
                                                <div class="permission-name">
                                                    <span class="material-symbols-rounded">{{ $icono }}</span>
                                                    {{ $nombreEs }}
                                                </div>
                                                @if($descripcion)
                                                    <div class="permission-description">{{ $descripcion }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-actions">
                        <a href="{{ route('admin.roles.index') }}" class="btn-submit btn-cancel">
                            <span class="material-symbols-rounded" style="font-size: 20px;">close</span>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-submit btn-primary-submit">
                            <span class="material-symbols-rounded" style="font-size: 20px;">save</span>
                            Actualizar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Sincronizar campos del sidebar con el formulario
(function () {
    var form = document.getElementById('editRoleForm');
    var nameInput = document.getElementById('roleNameInput');
    var descInput = document.getElementById('roleDescriptionInput');
    var accionInput = document.getElementById('roleAccionInput');
    var nameHidden = document.getElementById('roleNameHidden');
    var descHidden = document.getElementById('roleDescriptionHidden');
    var accionHidden = document.getElementById('roleAccionHidden');

    if (!form || !nameInput || !nameHidden) return;

    form.addEventListener('submit', function () {
        if (!nameInput.disabled) {
            nameHidden.value = nameInput.value;
        }
        if (descInput && descHidden) {
            descHidden.value = descInput.value;
        }
        if (accionInput && accionHidden) {
            accionHidden.value = accionInput.value;
        }
    });
})();

function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = true;
        cb.closest('.permission-item').classList.add('selected');
    });
}

function clearAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = false;
        cb.closest('.permission-item').classList.remove('selected');
    });
}

document.querySelectorAll('.permission-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('.permission-item').classList.toggle('selected', this.checked);
    });
});

document.getElementById('editRoleForm').addEventListener('submit', function(e) {
    if (document.querySelectorAll('.permission-checkbox:checked').length === 0) {
        if (!confirm('¿Estás seguro de quitar todos los permisos? Esto puede afectar a los usuarios con este rol.')) {
            e.preventDefault();
        }
    }
});
</script>
@endsection
