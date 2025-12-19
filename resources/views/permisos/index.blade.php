@extends('layouts.app')

@section('title', 'Matriz de Permisos Granulares')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/views/permisos.css') }}">
@endpush

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
        --primary-blue: #116dff;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
    }

    .page-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: 20px;
        padding: 32px;
        color: white;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header-icon {
        width: 64px;
        height: 64px;
        background: rgba(255,255,255,0.1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }

    .header-text h1 {
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 6px 0;
    }

    .header-text p {
        font-size: 14px;
        opacity: 0.85;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-header {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        background: rgba(255,255,255,0.1);
        color: white;
    }

    .btn-header:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-1px);
    }

    .btn-header.primary {
        background: var(--primary-blue);
    }

    .btn-header.primary:hover {
        background: #0056d6;
    }

    .info-card {
        background: linear-gradient(135deg, rgba(17, 109, 255, 0.08), rgba(99, 102, 241, 0.05));
        border: 1px solid rgba(17, 109, 255, 0.2);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 28px;
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .info-card-header .material-symbols-rounded {
        color: var(--primary-blue);
        font-size: 28px;
    }

    .info-card-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--slate-800);
        margin: 0;
    }

    .info-card-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }

    .info-item {
        display: flex;
        gap: 12px;
    }

    .info-item-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-blue);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    .info-item-text h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--slate-800);
        margin: 0 0 4px 0;
    }

    .info-item-text p {
        font-size: 13px;
        color: var(--slate-600);
        margin: 0;
        line-height: 1.4;
    }

    .flow-banner {
        background: linear-gradient(135deg, var(--slate-700), var(--slate-800));
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        color: white;
    }

    .flow-step {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        background: rgba(255,255,255,0.1);
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
    }

    .flow-step .material-symbols-rounded {
        font-size: 20px;
    }

    .flow-arrow {
        font-size: 24px;
        opacity: 0.6;
    }

    .role-section {
        background: white;
        border-radius: 18px;
        border: 1px solid var(--slate-200);
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .role-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, var(--slate-50), white);
        border-bottom: 1px solid var(--slate-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .role-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 18px;
        font-weight: 700;
        color: var(--slate-800);
    }

    .role-title .material-symbols-rounded {
        font-size: 28px;
        color: var(--primary-blue);
    }

    .role-badge {
        background: var(--primary-blue);
        color: white;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
    }

    .role-description {
        font-size: 13px;
        color: var(--slate-500);
        margin-left: 40px;
        margin-top: 4px;
    }

    .permissions-table-wrapper {
        overflow-x: auto;
    }

    .permissions-table {
        width: 100%;
        border-collapse: collapse;
    }

    .permissions-table th {
        padding: 14px 12px;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        color: var(--slate-600);
        background: var(--slate-50);
        border-bottom: 1px solid var(--slate-200);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .permissions-table th:first-child {
        text-align: left;
        padding-left: 24px;
    }

    .permissions-table td {
        padding: 16px 12px;
        border-bottom: 1px solid var(--slate-100);
        text-align: center;
    }

    .permissions-table td:first-child {
        text-align: left;
        padding-left: 24px;
    }

    .permissions-table tbody tr:hover {
        background: var(--slate-50);
    }

    .permissions-table tbody tr:last-child td {
        border-bottom: none;
    }

    .etapa-cell strong {
        color: var(--slate-800);
        display: block;
    }

    .etapa-cell .desc {
        font-size: 12px;
        color: var(--slate-500);
        margin-top: 2px;
    }

    .check-icon {
        color: var(--success);
        font-size: 22px;
    }

    .cross-icon {
        color: var(--slate-300);
        font-size: 22px;
    }

    .btn-icon {
        background: none;
        border: none;
        padding: 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-icon:hover {
        background: var(--slate-100);
    }

    .btn-icon .material-symbols-rounded {
        font-size: 20px;
        color: var(--slate-500);
    }

    .empty-state {
        padding: 48px 24px;
        text-align: center;
        color: var(--slate-500);
    }

    .empty-state .material-symbols-rounded {
        font-size: 56px;
        color: var(--slate-300);
        margin-bottom: 16px;
    }

    .empty-state p {
        font-size: 15px;
        margin-bottom: 20px;
    }

    .legend {
        display: flex;
        gap: 24px;
        justify-content: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--slate-600);
    }
</style>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <span class="material-symbols-rounded">admin_panel_settings</span>
            </div>
            <div class="header-text">
                <h1>Matriz de Permisos Granulares</h1>
                <p>Gestión avanzada de capacidades por rol y etapa del flujo</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.roles.index') }}" class="btn-header">
                <span class="material-symbols-rounded">arrow_back</span>
                Volver a Roles
            </a>
            <a href="{{ route('admin.permisos.create') }}" class="btn-header primary">
                <span class="material-symbols-rounded">add</span>
                Nuevo Permiso
            </a>
        </div>
    </div>

    <!-- Info Card - ¿Qué son los permisos granulares? -->
    <div class="info-card">
        <div class="info-card-header">
            <span class="material-symbols-rounded">help</span>
            <h3>¿Qué son los Permisos Granulares?</h3>
        </div>
        <div class="info-card-content">
            <div class="info-item">
                <div class="info-item-icon">
                    <span class="material-symbols-rounded">tune</span>
                </div>
                <div class="info-item-text">
                    <h4>Control Detallado</h4>
                    <p>Los permisos granulares permiten definir exactamente qué acciones puede realizar cada rol en cada etapa del flujo de aprobación.</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-item-icon">
                    <span class="material-symbols-rounded">account_tree</span>
                </div>
                <div class="info-item-text">
                    <h4>Por Etapa del Flujo</h4>
                    <p>Cada rol tiene permisos específicos según la etapa: Auxiliar crea, Administrador aprueba, Tesorería paga.</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-item-icon">
                    <span class="material-symbols-rounded">security</span>
                </div>
                <div class="info-item-text">
                    <h4>Seguridad Avanzada</h4>
                    <p>Restringe acciones como aprobar, eliminar o registrar pagos solo a los roles autorizados en cada momento.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flow Banner -->
    <div class="flow-banner">
        <span style="font-size: 14px; opacity: 0.8; margin-right: 12px;">Flujo de Aprobación:</span>
        @php
            $etapas = config('permisos_traducidos.etapas_flujo', []);
        @endphp
        @foreach($etapas as $key => $etapa)
            <div class="flow-step" style="background: {{ $etapa['color'] ?? '#3b82f6' }}33;">
                <span class="material-symbols-rounded">{{ $etapa['icono'] ?? 'check' }}</span>
                {{ $etapa['nombre_es'] ?? ucfirst($key) }}
            </div>
            @if(!$loop->last)
                <span class="flow-arrow material-symbols-rounded">arrow_forward</span>
            @endif
        @endforeach
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <span class="material-symbols-rounded check-icon">check_circle</span>
            Permitido
        </div>
        <div class="legend-item">
            <span class="material-symbols-rounded cross-icon">cancel</span>
            No permitido
        </div>
    </div>

    @php
        $rolesTraducidos = config('permisos_traducidos.roles', []);
        $granularesTraducidos = config('permisos_traducidos.granulares', []);
    @endphp

    <!-- Roles Sections -->
    @foreach($roles as $role)
        @php
            $rolInfo = $rolesTraducidos[$role->name] ?? null;
            $rolNombre = $rolInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $role->name));
            $rolDesc = $rolInfo['descripcion'] ?? '';
            $rolIcono = $rolInfo['icono'] ?? 'badge';
            $rolColor = $rolInfo['color'] ?? '#3b82f6';
        @endphp
        <div class="role-section">
            <div class="role-header">
                <div>
                    <div class="role-title">
                        <span class="material-symbols-rounded" style="color: {{ $rolColor }};">{{ $rolIcono }}</span>
                        {{ $rolNombre }}
                    </div>
                    @if($rolDesc)
                        <div class="role-description">{{ $rolDesc }}</div>
                    @endif
                </div>
                <span class="role-badge" style="background: {{ $rolColor }};">
                    {{ $permisosPorRol[$role->name]->count() }} Configuraciones
                </span>
            </div>

            @if($permisosPorRol[$role->name]->count() > 0)
                <div class="permissions-table-wrapper">
                    <table class="permissions-table">
                        <thead>
                            <tr>
                                <th style="width: 200px;">Etapa / Contexto</th>
                                <th>{{ $granularesTraducidos['puede_crear']['nombre_es'] ?? 'Crear' }}</th>
                                <th>{{ $granularesTraducidos['puede_leer']['nombre_es'] ?? 'Leer' }}</th>
                                <th>{{ $granularesTraducidos['puede_editar']['nombre_es'] ?? 'Editar' }}</th>
                                <th>{{ $granularesTraducidos['puede_eliminar']['nombre_es'] ?? 'Eliminar' }}</th>
                                <th>{{ $granularesTraducidos['puede_aprobar']['nombre_es'] ?? 'Aprobar' }}</th>
                                <th>{{ $granularesTraducidos['puede_rechazar']['nombre_es'] ?? 'Rechazar' }}</th>
                                <th>{{ $granularesTraducidos['puede_registrar_pago']['nombre_es'] ?? 'Pagos' }}</th>
                                <th>Docs</th>
                                <th style="width: 100px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permisosPorRol[$role->name] as $permiso)
                                @php
                                    $etapaInfo = $etapas[$permiso->etapa_flujo] ?? null;
                                    $etapaNombre = $etapaInfo['nombre_es'] ?? ($permiso->etapa_flujo ? ucfirst(str_replace('_', ' ', $permiso->etapa_flujo)) : 'General / Global');
                                @endphp
                                <tr>
                                    <td class="etapa-cell">
                                        <strong>{{ $etapaNombre }}</strong>
                                        @if($permiso->descripcion)
                                            <div class="desc">{{ $permiso->descripcion }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="material-symbols-rounded {{ $permiso->puede_crear ? 'check-icon' : 'cross-icon' }}">
                                            {{ $permiso->puede_crear ? 'check_circle' : 'cancel' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="material-symbols-rounded {{ $permiso->puede_leer ? 'check-icon' : 'cross-icon' }}">
                                            {{ $permiso->puede_leer ? 'check_circle' : 'cancel' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="material-symbols-rounded {{ $permiso->puede_editar ? 'check-icon' : 'cross-icon' }}">
                                            {{ $permiso->puede_editar ? 'check_circle' : 'cancel' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="material-symbols-rounded {{ $permiso->puede_eliminar ? 'check-icon' : 'cross-icon' }}">
                                            {{ $permiso->puede_eliminar ? 'check_circle' : 'cancel' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="material-symbols-rounded {{ $permiso->puede_aprobar ? 'check-icon' : 'cross-icon' }}">
                                            {{ $permiso->puede_aprobar ? 'check_circle' : 'cancel' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="material-symbols-rounded {{ $permiso->puede_rechazar ? 'check-icon' : 'cross-icon' }}">
                                            {{ $permiso->puede_rechazar ? 'check_circle' : 'cancel' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="material-symbols-rounded {{ $permiso->puede_registrar_pago ? 'check-icon' : 'cross-icon' }}">
                                            {{ $permiso->puede_registrar_pago ? 'check_circle' : 'cancel' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="material-symbols-rounded {{ ($permiso->puede_subir_documentos || $permiso->puede_descargar_documentos) ? 'check-icon' : 'cross-icon' }}">
                                            {{ ($permiso->puede_subir_documentos || $permiso->puede_descargar_documentos) ? 'check_circle' : 'cancel' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 4px; justify-content: center;">
                                            <a href="{{ route('admin.permisos.edit', $permiso->id) }}" class="btn-icon" title="Editar">
                                                <span class="material-symbols-rounded">edit</span>
                                            </a>
                                            <form action="{{ route('admin.permisos.destroy', $permiso->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta configuración de permisos?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon" title="Eliminar">
                                                    <span class="material-symbols-rounded" style="color: #ef4444;">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <span class="material-symbols-rounded">rule_settings</span>
                    <p>No hay permisos granulares configurados para este rol.</p>
                    <a href="{{ route('admin.permisos.create') }}?role_id={{ $role->id }}" class="btn-header primary" style="display: inline-flex;">
                        <span class="material-symbols-rounded">add</span>
                        Configurar Permisos
                    </a>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
