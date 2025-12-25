@extends('layouts.app')

@section('title', 'Historial de Cuenta #' . $cuenta->numero)

@section('content')
<style>
    .historial-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }
    
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
        gap: 24px;
        flex-wrap: wrap;
    }
    
    .cuenta-info {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        flex: 1;
        min-width: 300px;
    }
    
    .cuenta-numero {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 8px;
    }
    
    .cuenta-beneficiario {
        font-size: 16px;
        color: #6b7280;
        margin-bottom: 16px;
    }
    
    .cuenta-meta {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }
    
    .meta-item {
        display: flex;
        flex-direction: column;
    }
    
    .meta-label {
        font-size: 12px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .meta-value {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
    }
    
    .estado-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    
    .estado-en_revision { background: #FEF3C7; color: #92400E; }
    .estado-en_correccion { background: #FED7AA; color: #9A3412; }
    .estado-aprobado, .estado-aprobado_tesoreria { background: #D1FAE5; color: #065F46; }
    .estado-rechazado { background: #FEE2E2; color: #991B1B; }
    .estado-pagado { background: #D1FAE5; color: #065F46; }
    .estado-anulado { background: #E5E7EB; color: #4B5563; }
    .estado-enviado_cliente { background: #DBEAFE; color: #1E40AF; }
    
    .timeline-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .timeline {
        position: relative;
        padding-left: 32px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 24px;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-icon {
        position: absolute;
        left: -32px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        z-index: 1;
    }
    
    .timeline-content {
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
        border-left: 3px solid #e5e7eb;
    }
    
    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .timeline-action {
        font-weight: 600;
        font-size: 14px;
    }
    
    .timeline-date {
        font-size: 12px;
        color: #9ca3af;
    }
    
    .timeline-user {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    
    .timeline-comment {
        font-size: 14px;
        color: #4b5563;
        background: white;
        padding: 12px;
        border-radius: 8px;
        margin-top: 8px;
        border: 1px solid #e5e7eb;
    }
    
    .actions-panel {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        position: sticky;
        top: 100px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        margin-bottom: 8px;
        transition: all 0.2s;
    }
    
    .btn-devolver {
        background: #FEF3C7;
        color: #92400E;
    }
    
    .btn-devolver:hover {
        background: #FDE68A;
    }
    
    .btn-anular {
        background: #FEE2E2;
        color: #991B1B;
    }
    
    .btn-anular:hover {
        background: #FECACA;
    }
    
    .btn-volver {
        background: #f3f4f6;
        color: #4b5563;
    }
    
    .btn-volver:hover {
        background: #e5e7eb;
    }
    
    /* Modal styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal-box {
        background: white;
        border-radius: 16px;
        padding: 24px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
    }
    
    .form-group {
        margin-bottom: 16px;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #374151;
    }
    
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }
    
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a2e;
    }
    
    .stat-label {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    
    @media (max-width: 768px) {
        .header-section {
            flex-direction: column;
        }
        
        .cuenta-meta {
            flex-direction: column;
            gap: 12px;
        }
    }
</style>

<div class="historial-container">
    <!-- Header con info de la cuenta -->
    <div class="header-section">
        <div class="cuenta-info" style="flex: 2;">
            <div class="cuenta-numero">
                <span class="material-symbols-rounded" style="vertical-align: middle;">receipt_long</span>
                Cuenta de Cobro #{{ $cuenta->numero }}
            </div>
            <div class="cuenta-beneficiario">
                {{ $cuenta->nombre_beneficiario }} - {{ $cuenta->identificacion }}
            </div>
            
            <div class="cuenta-meta">
                <div class="meta-item">
                    <span class="meta-label">Valor Total</span>
                    <span class="meta-value">${{ number_format($cuenta->valor_total, 0, ',', '.') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Fecha Emisión</span>
                    <span class="meta-value">{{ $cuenta->fecha_emision?->format('d/m/Y') ?? 'N/A' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Estado</span>
                    <span class="estado-badge estado-{{ $cuenta->estado_aprobacion }}">
                        {{ ucfirst(str_replace('_', ' ', $cuenta->estado_aprobacion)) }}
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Creado por</span>
                    <span class="meta-value">{{ $cuenta->user?->name ?? 'Sistema' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Panel de acciones -->
        @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_programa') || auth()->user()->hasRole('administrador') || auth()->user()->hasRole('tesoreria'))
        <div class="actions-panel" style="min-width: 250px;">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                <span class="material-symbols-rounded" style="vertical-align: middle;">settings</span>
                Acciones
            </h3>
            
            @if($cuenta->estado_aprobacion !== 'anulado')
                <button class="action-btn btn-devolver" onclick="openModal('devolverModal')">
                    <span class="material-symbols-rounded">undo</span>
                    Devolver Cuenta
                </button>
                
                @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_programa'))
                <button class="action-btn btn-anular" onclick="openModal('anularModal')">
                    <span class="material-symbols-rounded">block</span>
                    Anular Cuenta
                </button>
                @endif
            @else
                <div style="padding: 16px; background: #f3f4f6; border-radius: 8px; text-align: center; color: #6b7280;">
                    <span class="material-symbols-rounded" style="font-size: 32px; display: block; margin-bottom: 8px;">block</span>
                    Esta cuenta está anulada
                </div>
            @endif
            
            <a href="{{ route('cuentas_cobro.show', $cuenta->id) }}" class="action-btn btn-volver" style="text-decoration: none; margin-top: 16px;">
                <span class="material-symbols-rounded">visibility</span>
                Ver Cuenta Completa
            </a>
            
            <a href="{{ route('cuentas_cobro.index') }}" class="action-btn btn-volver" style="text-decoration: none;">
                <span class="material-symbols-rounded">arrow_back</span>
                Volver al Listado
            </a>
        </div>
        @endif
    </div>
    
    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $cuenta->historial->count() }}</div>
            <div class="stat-label">Eventos en Historial</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $cuenta->historial->where('accion', 'devuelto')->count() + $cuenta->historial->where('accion', 'devuelto_general')->count() }}</div>
            <div class="stat-label">Devoluciones</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $cuenta->interacciones->count() }}</div>
            <div class="stat-label">Comentarios</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $cuenta->soportes->count() }}</div>
            <div class="stat-label">Soportes</div>
        </div>
    </div>
    
    <!-- Timeline de historial -->
    <div class="timeline-section">
        <h2 class="section-title">
            <span class="material-symbols-rounded">history</span>
            Historial Completo de Cambios
        </h2>
        
        <div class="timeline">
            @forelse($cuenta->historial as $item)
            <div class="timeline-item">
                <div class="timeline-icon" style="background-color: {{ $item->getColor() }};">
                    <span class="material-symbols-rounded" style="font-size: 14px;">{{ $item->getIcono() }}</span>
                </div>
                <div class="timeline-content" style="border-left-color: {{ $item->getColor() }};">
                    <div class="timeline-header">
                        <span class="timeline-action" style="color: {{ $item->getColor() }};">
                            {{ $item->getEtiqueta() }}
                        </span>
                        <span class="timeline-date">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                            ({{ $item->created_at->diffForHumans() }})
                        </span>
                    </div>
                    <div class="timeline-user">
                        <span class="material-symbols-rounded" style="font-size: 14px; vertical-align: middle;">person</span>
                        {{ $item->user?->name ?? 'Sistema' }}
                        @if($item->user?->role)
                            <span style="opacity: 0.7;">({{ ucfirst(str_replace('_', ' ', $item->user->role->name)) }})</span>
                        @endif
                    </div>
                    @if($item->comentario)
                    <div class="timeline-comment">
                        {{ $item->comentario }}
                    </div>
                    @endif
                    @if($item->estado_anterior || $item->estado_nuevo)
                    <div style="font-size: 12px; color: #9ca3af; margin-top: 8px;">
                        @if($item->estado_anterior)
                            <span>{{ ucfirst(str_replace('_', ' ', $item->estado_anterior)) }}</span>
                        @endif
                        @if($item->estado_anterior && $item->estado_nuevo)
                            → 
                        @endif
                        @if($item->estado_nuevo)
                            <span style="font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $item->estado_nuevo)) }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                <span class="material-symbols-rounded" style="font-size: 48px; display: block; margin-bottom: 16px;">history</span>
                No hay registros en el historial
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Interacciones/Comentarios -->
    @if($cuenta->interacciones->count() > 0)
    <div class="timeline-section">
        <h2 class="section-title">
            <span class="material-symbols-rounded">chat</span>
            Comentarios e Interacciones
        </h2>
        
        <div class="timeline">
            @foreach($cuenta->interacciones as $interaccion)
            <div class="timeline-item">
                <div class="timeline-icon" style="background-color: #5856D6;">
                    <span class="material-symbols-rounded" style="font-size: 14px;">comment</span>
                </div>
                <div class="timeline-content" style="border-left-color: #5856D6;">
                    <div class="timeline-header">
                        <span class="timeline-action" style="color: #5856D6;">
                            {{ ucfirst($interaccion->tipo ?? 'Comentario') }}
                        </span>
                        <span class="timeline-date">
                            {{ $interaccion->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <div class="timeline-user">
                        <span class="material-symbols-rounded" style="font-size: 14px; vertical-align: middle;">person</span>
                        {{ $interaccion->user?->name ?? 'Sistema' }}
                    </div>
                    <div class="timeline-comment">
                        {{ $interaccion->mensaje ?? $interaccion->contenido }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Modal Devolver -->
<div class="modal-overlay" id="devolverModal">
    <div class="modal-box">
        <h3 class="modal-title">
            <span class="material-symbols-rounded" style="vertical-align: middle; color: #FF9500;">undo</span>
            Devolver Cuenta de Cobro
        </h3>
        <p style="color: #6b7280; margin-bottom: 20px;">
            Esta acción devolverá la cuenta para que pueda ser modificada. 
            Útil para ajustar plazos, montos o corregir información.
        </p>
        
        <form action="{{ route('cuentas_cobro.devolver_general', $cuenta->id) }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Devolver a</label>
                <select name="devolver_a" class="form-select" required>
                    <option value="auxiliar">Auxiliar (creador original)</option>
                    <option value="administrador">Administrador</option>
                    <option value="tesoreria">Tesorería</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Motivo de la devolución *</label>
                <textarea name="motivo" class="form-textarea" required 
                    placeholder="Explique por qué se devuelve esta cuenta..."></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="action-btn btn-volver" onclick="closeModal('devolverModal')">
                    Cancelar
                </button>
                <button type="submit" class="action-btn btn-devolver">
                    <span class="material-symbols-rounded">undo</span>
                    Devolver Cuenta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Anular -->
<div class="modal-overlay" id="anularModal">
    <div class="modal-box">
        <h3 class="modal-title">
            <span class="material-symbols-rounded" style="vertical-align: middle; color: #FF3B30;">block</span>
            Anular Cuenta de Cobro
        </h3>
        <p style="color: #991B1B; background: #FEE2E2; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <strong>⚠️ Atención:</strong> Esta acción anulará permanentemente la cuenta. 
            La cuenta quedará archivada y no podrá ser modificada.
        </p>
        
        <form action="{{ route('cuentas_cobro.anular', $cuenta->id) }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Motivo de la anulación *</label>
                <textarea name="motivo_anulacion" class="form-textarea" required minlength="10"
                    placeholder="Explique detalladamente por qué se anula esta cuenta..."></textarea>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" required>
                    <span>Confirmo que deseo anular esta cuenta de cobro</span>
                </label>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="action-btn btn-volver" onclick="closeModal('anularModal')">
                    Cancelar
                </button>
                <button type="submit" class="action-btn btn-anular">
                    <span class="material-symbols-rounded">block</span>
                    Anular Cuenta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Cerrar modal al hacer clic fuera
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>
@endsection
