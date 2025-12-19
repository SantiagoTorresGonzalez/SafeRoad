@extends('layouts.app')

@section('title', 'Crear Nuevo Rol')

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
        --purple-gradient: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
    }

    .role-header {
        background: var(--purple-gradient);
        border-radius: 24px;
        padding: 40px 32px;
        color: white;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 24px;
        box-shadow: 0 10px 40px rgba(99, 102, 241, 0.2);
    }

    .role-icon-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
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

    .card {
        background: white;
        border-radius: 18px;
        padding: 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--slate-200);
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--slate-800);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--slate-100);
    }

    .section-title .material-symbols-rounded {
        color: var(--primary-blue);
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--slate-700);
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--slate-200);
        font-size: 15px;
        color: var(--slate-800);
        transition: all 0.2s;
        background: var(--slate-50);
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-blue);
        background: white;
        box-shadow: 0 0 0 3px rgba(17, 109, 255, 0.1);
    }

    .permission-category {
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .category-header {
        padding: 16px 20px;
        background: linear-gradient(135deg, var(--slate-100), var(--slate-50));
        border-bottom: 1px solid var(--slate-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }

    .category-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 16px;
        font-weight: 700;
        color: var(--slate-800);
    }

    .category-title .material-symbols-rounded {
        font-size: 24px;
        color: var(--primary-blue);
    }

    .category-description {
        font-size: 12px;
        color: var(--slate-500);
        margin-top: 4px;
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
        padding: 20px;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
    }

    .permission-card {
        background: white;
        border: 2px solid var(--slate-200);
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .permission-card:hover {
        border-color: var(--primary-blue);
        background: rgba(17, 109, 255, 0.02);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .permission-card.selected {
        border-color: var(--primary-blue);
        background: rgba(17, 109, 255, 0.05);
    }

    .permission-checkbox {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        border: 2px solid var(--slate-300);
        appearance: none;
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
        margin-top: 2px;
        transition: all 0.2s;
    }

    .permission-checkbox:checked {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
    }

    .permission-checkbox:checked::after {
        content: 'check';
        font-family: 'Material Symbols Rounded';
        color: white;
        font-size: 16px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-weight: bold;
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
        margin-top: 4px;
        line-height: 1.4;
    }

    .btn-submit {
        background: var(--primary-blue);
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-submit:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(17, 109, 255, 0.2);
    }

    .btn-cancel {
        background: white;
        color: var(--slate-600);
        border: 1px solid var(--slate-200);
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel:hover {
        background: var(--slate-50);
        color: var(--slate-800);
    }

    .actions-footer {
        display: flex;
        gap: 16px;
        margin-top: 32px;
        padding-top: 32px;
        border-top: 1px solid var(--slate-200);
    }

    .info-banner {
        background: linear-gradient(135deg, rgba(17, 109, 255, 0.08), rgba(99, 102, 241, 0.05));
        border: 1px solid rgba(17, 109, 255, 0.2);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .info-banner .material-symbols-rounded {
        color: var(--primary-blue);
        font-size: 24px;
    }

    .info-banner-content h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--slate-800);
        margin: 0 0 4px 0;
    }

    .info-banner-content p {
        font-size: 13px;
        color: var(--slate-600);
        margin: 0;
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

    @media (max-width: 768px) {
        .permissions-grid {
            grid-template-columns: 1fr;
        }
        .role-header {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="container">
    <a href="{{ route('admin.roles.index') }}" class="back-link">
        <span class="material-symbols-rounded">arrow_back</span>
        Volver a roles
    </a>

    <div class="role-header">
        <div class="role-icon-large">
            <span class="material-symbols-rounded">add_moderator</span>
        </div>
        <div class="role-header-content">
            <h1>Crear Nuevo Rol</h1>
            <p>Define un nuevo perfil de acceso con sus permisos asociados al sistema</p>
        </div>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        
        <div class="card">
            <div class="section-title">
                <span class="material-symbols-rounded">info</span>
                Información Básica
            </div>

            <div class="form-group">
                <label class="form-label">Nombre del Rol <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" class="form-input" placeholder="Ej: Revisor de Documentos" required value="{{ old('name') }}">
                @error('name')
                    <span style="color: #ef4444; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Descripción</label>
                <textarea name="description" class="form-input" rows="3" placeholder="Describe brevemente las responsabilidades de este rol...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Acción de Cumplimiento</label>
                <input type="text" name="accion_cumplimiento" class="form-input" value="{{ old('accion_cumplimiento') }}" placeholder="Ej: Aprobar pagos, Validar documentos, Enviar a DIAN">
                <small style="color: var(--slate-500); font-size: 12px; margin-top: 4px; display: block;">Define qué acción ejecuta este rol en el flujo de trabajo</small>
            </div>
        </div>

        <div class="card">
            <div class="section-title">
                <span class="material-symbols-rounded">security</span>
                Asignar Permisos
            </div>

            <div class="info-banner">
                <span class="material-symbols-rounded">lightbulb</span>
                <div class="info-banner-content">
                    <h4>¿Qué son los permisos?</h4>
                    <p>Los permisos definen qué acciones puede realizar un usuario con este rol. Selecciona los permisos apropiados según las responsabilidades del rol.</p>
                </div>
            </div>

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
                $traduccion = config('permisos_traducidos', []);
                $categorias = $traduccion['categorias'] ?? [];
                $permisosTraducidos = $traduccion['permisos'] ?? [];

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
                            <div>
                                <div class="category-title">
                                    <span class="material-symbols-rounded">{{ $catInfo['icono'] ?? 'folder' }}</span>
                                    {{ $catInfo['nombre'] }}
                                </div>
                                <div class="category-description">{{ $catInfo['descripcion'] ?? '' }}</div>
                            </div>
                            <span class="category-badge">{{ count($permisosPorCategoria[$catKey]) }} permisos</span>
                        </div>
                        <div class="category-body">
                            <div class="permissions-grid">
                                @foreach($permisosPorCategoria[$catKey] as $item)
                                    @php
                                        $perm = $item['permission'];
                                        $info = $item['info'];
                                        $nombreEs = $info['nombre_es'] ?? ucfirst(str_replace('_', ' ', $perm->name));
                                        $descripcion = $info['descripcion'] ?? '';
                                        $icono = $info['icono'] ?? 'check';
                                    @endphp
                                    <label class="permission-card">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $perm->id }}" 
                                               class="permission-checkbox"
                                               {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
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
                        <div>
                            <div class="category-title">
                                <span class="material-symbols-rounded">extension</span>
                                Otros Permisos
                            </div>
                            <div class="category-description">Permisos adicionales del sistema</div>
                        </div>
                        <span class="category-badge">{{ count($permisosPorCategoria['otros']) }} permisos</span>
                    </div>
                    <div class="category-body">
                        <div class="permissions-grid">
                            @foreach($permisosPorCategoria['otros'] as $item)
                                @php
                                    $perm = $item['permission'];
                                    $info = $item['info'];
                                    $nombreEs = $info['nombre_es'] ?? ucfirst(str_replace('_', ' ', $perm->name));
                                    $descripcion = $info['descripcion'] ?? '';
                                    $icono = $info['icono'] ?? 'check';
                                @endphp
                                <label class="permission-card">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $perm->id }}" 
                                           class="permission-checkbox"
                                           {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
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

            <div class="actions-footer">
                <button type="submit" class="btn-submit">
                    <span class="material-symbols-rounded">save</span>
                    Guardar Rol
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn-cancel">Cancelar</a>
            </div>
        </div>
    </form>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = true;
        cb.closest('.permission-card').classList.add('selected');
    });
}

function clearAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = false;
        cb.closest('.permission-card').classList.remove('selected');
    });
}

document.querySelectorAll('.permission-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('.permission-card').classList.toggle('selected', this.checked);
    });
});
</script>
@endsection
