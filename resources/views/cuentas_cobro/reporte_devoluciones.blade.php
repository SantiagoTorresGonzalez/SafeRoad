@extends('layouts.app')

@section('title', 'Reporte de Devoluciones y Anulaciones')

@section('content')
<style>
    .reporte-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a2e;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .stat-icon.devoluciones { background: linear-gradient(135deg, #FF9500, #FF6B00); }
    .stat-icon.rechazos { background: linear-gradient(135deg, #FF3B30, #D62828); }
    .stat-icon.anulaciones { background: linear-gradient(135deg, #8E8E93, #636366); }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a2e;
    }
    
    .stat-label {
        font-size: 13px;
        color: #6b7280;
    }
    
    .filters-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        align-items: end;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
    }
    
    .form-input, .form-select {
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .btn-filter {
        background: #00b5e2;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-filter:hover {
        background: #0097be;
    }
    
    .table-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        text-align: left;
        padding: 12px 16px;
        background: #f9fafb;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
    }
    
    .data-table tr:hover {
        background: #f9fafb;
    }
    
    .accion-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .accion-devuelto, .accion-devuelto_general {
        background: #FEF3C7;
        color: #92400E;
    }
    
    .accion-rechazado {
        background: #FEE2E2;
        color: #991B1B;
    }
    
    .accion-anulado {
        background: #E5E7EB;
        color: #4B5563;
    }
    
    .cuenta-link {
        color: #00b5e2;
        text-decoration: none;
        font-weight: 600;
    }
    
    .cuenta-link:hover {
        text-decoration: underline;
    }
    
    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }
    
    .empty-state .icon {
        font-size: 64px;
        margin-bottom: 16px;
    }
</style>

<div class="reporte-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <span class="material-symbols-rounded" style="vertical-align: middle;">history</span>
                Reporte de Devoluciones y Anulaciones
            </h1>
            <p style="color: #6b7280; margin-top: 8px;">
                Registro completo de todas las devoluciones, rechazos y anulaciones de cuentas de cobro
            </p>
        </div>
        
        <a href="{{ route('cuentas_cobro.index') }}" style="display: flex; align-items: center; gap: 8px; color: #6b7280; text-decoration: none;">
            <span class="material-symbols-rounded">arrow_back</span>
            Volver
        </a>
    </div>
    
    <!-- Estadísticas -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon devoluciones">
                <span class="material-symbols-rounded">undo</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total_devoluciones'] }}</div>
                <div class="stat-label">Total Devoluciones</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon rechazos">
                <span class="material-symbols-rounded">cancel</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total_rechazos'] }}</div>
                <div class="stat-label">Total Rechazos</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon anulaciones">
                <span class="material-symbols-rounded">block</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total_anulaciones'] }}</div>
                <div class="stat-label">Total Anulaciones</div>
            </div>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="filters-section">
        <form method="GET" action="{{ route('reportes.devoluciones') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-input" 
                        value="{{ request('fecha_desde') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-input"
                        value="{{ request('fecha_hasta') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Usuario</label>
                    <select name="usuario_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ request('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipo de Acción</label>
                    <select name="accion" class="form-select">
                        <option value="">Todas</option>
                        <option value="devuelto" {{ request('accion') == 'devuelto' ? 'selected' : '' }}>Devuelto</option>
                        <option value="devuelto_general" {{ request('accion') == 'devuelto_general' ? 'selected' : '' }}>Devuelto (General)</option>
                        <option value="rechazado" {{ request('accion') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                        <option value="anulado" {{ request('accion') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-filter">
                        <span class="material-symbols-rounded">filter_alt</span>
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Tabla de resultados -->
    <div class="table-section">
        @if($devoluciones->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Cuenta</th>
                    <th>Acción</th>
                    <th>Realizada por</th>
                    <th>Estado Anterior</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devoluciones as $item)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $item->created_at->format('d/m/Y') }}</div>
                        <div style="font-size: 12px; color: #9ca3af;">{{ $item->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                        @if($item->cuenta)
                            <a href="{{ route('cuentas_cobro.historial', $item->cuenta->id) }}" class="cuenta-link">
                                #{{ $item->cuenta->numero }}
                            </a>
                            <div style="font-size: 12px; color: #6b7280;">
                                {{ Str::limit($item->cuenta->nombre_beneficiario, 25) }}
                            </div>
                        @else
                            <span style="color: #9ca3af;">Cuenta eliminada</span>
                        @endif
                    </td>
                    <td>
                        <span class="accion-badge accion-{{ $item->accion }}">
                            @switch($item->accion)
                                @case('devuelto')
                                @case('devuelto_general')
                                    <span class="material-symbols-rounded" style="font-size: 14px;">undo</span>
                                    Devuelto
                                    @break
                                @case('rechazado')
                                    <span class="material-symbols-rounded" style="font-size: 14px;">cancel</span>
                                    Rechazado
                                    @break
                                @case('anulado')
                                    <span class="material-symbols-rounded" style="font-size: 14px;">block</span>
                                    Anulado
                                    @break
                            @endswitch
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: 500;">{{ $item->user?->name ?? 'Sistema' }}</div>
                        @if($item->user?->role)
                            <div style="font-size: 12px; color: #9ca3af;">
                                {{ ucfirst(str_replace('_', ' ', $item->user->role->name)) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span style="font-size: 13px; color: #6b7280;">
                            {{ $item->estado_anterior ? ucfirst(str_replace('_', ' ', $item->estado_anterior)) : '-' }}
                        </span>
                    </td>
                    <td>
                        <div style="max-width: 300px; font-size: 13px; color: #4b5563;">
                            {{ Str::limit($item->comentario, 100) }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="pagination-wrapper">
            {{ $devoluciones->withQueryString()->links() }}
        </div>
        @else
        <div class="empty-state">
            <span class="material-symbols-rounded icon">history</span>
            <h3 style="font-size: 18px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                No hay registros
            </h3>
            <p>No se encontraron devoluciones o anulaciones con los filtros seleccionados.</p>
        </div>
        @endif
    </div>
</div>
@endsection
