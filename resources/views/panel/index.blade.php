@extends('layouts.saferoad')

@section('title', 'Panel de autoridad — SafeRoad SC')

@push('styles')
<style>
    .panel-banner {
        background: linear-gradient(135deg, #116dff 0%, #0058d6 100%);
        border-radius: 12px;
        padding: 20px 24px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .panel-banner h2 {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .panel-banner p {
        font-size: 13px;
        opacity: 0.85;
        margin: 0;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon-naranja { background: #fff7ed; color: #f97316; }
    .stat-icon-azul    { background: #eff6ff; color: #116dff; }
    .stat-icon-verde   { background: #f0fdf4; color: #22c55e; }
    .stat-icon-gris    { background: #f8fafc; color: #94a3b8; }

    .stat-info-number {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
    }

    .stat-info-label {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* Filtros */
    .filtros-card {
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filtros-card label {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        margin-right: 4px;
    }

    .filtros-card select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 13px;
        color: #0f172a;
        background: white;
        outline: none;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
    }

    .filtros-card select:focus {
        border-color: #116dff;
        box-shadow: 0 0 0 3px rgba(17,109,255,0.1);
    }

    .btn-filtrar {
        background: #116dff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-filtrar:hover { background: #0058d6; }

    .btn-limpiar {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Tabla */
    .tabla-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .tabla-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .tabla-header h3 {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
    }

    .tabla-header span {
        font-size: 12px;
        color: #94a3b8;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        padding: 10px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }

    tbody td {
        padding: 12px 16px;
        font-size: 13px;
        color: #475569;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }

    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #fafbfc; }

    /* Badges de estado */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-pendiente  { background: #fff7ed; color: #f97316; }
    .badge-en_atencion { background: #eff6ff; color: #116dff; }
    .badge-resuelto   { background: #f0fdf4; color: #16a34a; }
    .badge-descartado { background: #f8fafc; color: #94a3b8; }

    /* Botón gestionar */
    .btn-gestionar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-gestionar:hover {
        background: #116dff;
        color: white;
        border-color: #116dff;
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal-overlay.visible { display: flex; }

    .modal-box {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 480px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        overflow: hidden;
        animation: slideIn 0.2s ease-out;
    }

    @keyframes slideIn {
        from { transform: translateY(-16px); opacity: 0; }
        to   { transform: translateY(0);     opacity: 1; }
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        padding: 4px;
        border-radius: 6px;
        display: flex;
        align-items: center;
    }

    .modal-close:hover { background: #f1f5f9; color: #475569; }

    .modal-body {
        padding: 20px 24px;
    }

    .modal-body .info-row {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .modal-body .info-label {
        font-weight: 600;
        color: #0f172a;
        min-width: 90px;
    }

    .modal-body .info-value {
        color: #475569;
    }

    .modal-form-group {
        margin-top: 16px;
    }

    .modal-form-group label {
        display: block;
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        margin-bottom: 4px;
    }

    .modal-form-group select,
    .modal-form-group textarea {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        color: #0f172a;
        background: white;
        outline: none;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }

    .modal-form-group select:focus,
    .modal-form-group textarea:focus {
        border-color: #116dff;
        box-shadow: 0 0 0 3px rgba(17,109,255,0.1);
    }

    .modal-form-group textarea { resize: vertical; min-height: 80px; }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
    }

    .btn-guardar {
        background: #116dff;
        color: white;
        border: none;
        padding: 9px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-guardar:hover { background: #0058d6; }

    .btn-cancelar-modal {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 9px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
    }

    .alerta-modal {
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 12px;
        display: none;
    }

    .alerta-modal-ok  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .alerta-modal-err { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

    /* Paginación */
    .paginacion {
        padding: 16px 20px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: center;
    }

    .empty-state {
        padding: 48px 20px;
        text-align: center;
        color: #94a3b8;
    }

    .empty-state span {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
    }
</style>
@endpush

@section('content')

    {{-- Banner --}}
    <div class="panel-banner">
        <div>
            <h2>
                <span class="material-symbols-rounded" style="vertical-align: middle; font-size: 22px;">admin_panel_settings</span>
                Panel de gestión — Autoridad municipal
            </h2>
            <p>Valida y gestiona los reportes ciudadanos de siniestros viales</p>
        </div>
        <a href="{{ url('/mapa') }}" style="color: white; font-size: 13px; opacity: 0.85; text-decoration: none; display: flex; align-items: center; gap: 6px;">
            <span class="material-symbols-rounded" style="font-size: 18px;">map</span>
            Ver mapa público
        </a>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon stat-icon-naranja">
                <span class="material-symbols-rounded">pending</span>
            </div>
            <div>
                <div class="stat-info-number">{{ $stats['pendientes'] }}</div>
                <div class="stat-info-label">Pendientes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-azul">
                <span class="material-symbols-rounded">engineering</span>
            </div>
            <div>
                <div class="stat-info-number">{{ $stats['en_atencion'] }}</div>
                <div class="stat-info-label">En atención</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-verde">
                <span class="material-symbols-rounded">check_circle</span>
            </div>
            <div>
                <div class="stat-info-number">{{ $stats['resueltos'] }}</div>
                <div class="stat-info-label">Resueltos</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-gris">
                <span class="material-symbols-rounded">cancel</span>
            </div>
            <div>
                <div class="stat-info-number">{{ $stats['descartados'] }}</div>
                <div class="stat-info-label">Descartados</div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('panel.index') }}">
        <div class="filtros-card">
            <span class="material-symbols-rounded" style="color: #94a3b8; font-size: 20px;">filter_list</span>

            <label>Municipio</label>
            <select name="municipio">
                <option value="">Todos</option>
                @foreach(['Cajicá','Chía','Cogua','Cota','Gachancipá','Nemocón','Sopó','Tabio','Tenjo','Tocancipá','Zipaquirá'] as $mun)
                    <option value="{{ $mun }}" {{ request('municipio') == $mun ? 'selected' : '' }}>{{ $mun }}</option>
                @endforeach
            </select>

            <label>Estado</label>
            <select name="estado">
                <option value="">Todos</option>
                <option value="pendiente"   {{ request('estado') == 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                <option value="en_atencion" {{ request('estado') == 'en_atencion' ? 'selected' : '' }}>En atención</option>
                <option value="resuelto"    {{ request('estado') == 'resuelto'    ? 'selected' : '' }}>Resuelto</option>
                <option value="descartado"  {{ request('estado') == 'descartado'  ? 'selected' : '' }}>Descartado</option>
            </select>

            <button type="submit" class="btn-filtrar">
                <span class="material-symbols-rounded" style="font-size: 16px;">search</span>
                Filtrar
            </button>
            <a href="{{ route('panel.index') }}" class="btn-limpiar">
                <span class="material-symbols-rounded" style="font-size: 16px;">close</span>
                Limpiar
            </a>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="tabla-card">
        <div class="tabla-header">
            <h3>Reportes ciudadanos</h3>
            <span>{{ $reportes->total() }} reportes encontrados</span>
        </div>

        @if($reportes->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Municipio</th>
                    <th>Tipo de riesgo</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportes as $reporte)
                <tr>
                    <td style="color: #94a3b8; font-size: 12px;">{{ $reporte->id }}</td>
                    <td style="font-weight: 500; color: #0f172a;">{{ $reporte->municipio }}</td>
                    <td>{{ str_replace('_', ' ', $reporte->tipo_riesgo) }}</td>
                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $reporte->descripcion ?? '—' }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $reporte->estado }}">
                            {{ match($reporte->estado) {
                                'pendiente'   => '● Pendiente',
                                'en_atencion' => '● En atención',
                                'resuelto'    => '● Resuelto',
                                'descartado'  => '● Descartado',
                                default       => $reporte->estado
                            } }}
                        </span>
                    </td>
                    <td style="font-size: 12px; color: #94a3b8;">
                        {{ $reporte->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <button class="btn-gestionar" onclick="abrirModal({{ $reporte->id }}, '{{ $reporte->municipio }}', '{{ str_replace('_', ' ', $reporte->tipo_riesgo) }}', '{{ $reporte->estado }}', '{{ addslashes($reporte->descripcion ?? '') }}', '{{ addslashes($reporte->notas_autoridad ?? '') }}')">
                            <span class="material-symbols-rounded" style="font-size: 14px;">edit</span>
                            Gestionar
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="paginacion">
            {{ $reportes->appends(request()->query())->links() }}
        </div>

        @else
        <div class="empty-state">
            <span class="material-symbols-rounded" style="font-size: 48px; color: #cbd5e1;">inbox</span>
            <p style="font-size: 14px; font-weight: 500; color: #475569; margin: 0 0 4px;">No hay reportes</p>
            <p style="font-size: 13px; color: #94a3b8; margin: 0;">No se encontraron reportes con los filtros seleccionados.</p>
        </div>
        @endif
    </div>

    {{-- Modal gestión --}}
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>
                    <span class="material-symbols-rounded" style="vertical-align: middle; font-size: 20px; color: #116dff;">edit_location</span>
                    Gestionar reporte
                </h3>
                <button class="modal-close" onclick="cerrarModal()">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alerta-modal alerta-modal-ok" id="alerta-ok">
                    ✅ Reporte actualizado correctamente.
                </div>
                <div class="alerta-modal alerta-modal-err" id="alerta-err">
                    ❌ Error al actualizar. Intenta de nuevo.
                </div>

                <div class="info-row">
                    <span class="info-label">Municipio:</span>
                    <span class="info-value" id="modal-municipio"></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value" id="modal-tipo"></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Descripción:</span>
                    <span class="info-value" id="modal-descripcion"></span>
                </div>

                <div class="modal-form-group">
                    <label>Cambiar estado</label>
                    <select id="modal-estado">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_atencion">En atención</option>
                        <option value="resuelto">Resuelto</option>
                        <option value="descartado">Descartado</option>
                    </select>
                </div>

                <div class="modal-form-group">
                    <label>Notas de seguimiento (opcional)</label>
                    <textarea id="modal-notas" placeholder="Agrega notas sobre la intervención realizada..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-cancelar-modal" onclick="cerrarModal()">Cancelar</button>
                <button class="btn-guardar" onclick="guardarCambios()">
                    <span class="material-symbols-rounded" style="font-size: 16px;">save</span>
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    let reporteActualId = null;

    function abrirModal(id, municipio, tipo, estado, descripcion, notas) {
        reporteActualId = id;
        document.getElementById('modal-municipio').textContent  = municipio;
        document.getElementById('modal-tipo').textContent       = tipo;
        document.getElementById('modal-descripcion').textContent = descripcion || '—';
        document.getElementById('modal-estado').value           = estado;
        document.getElementById('modal-notas').value            = notas || '';
        document.getElementById('alerta-ok').style.display      = 'none';
        document.getElementById('alerta-err').style.display     = 'none';
        document.getElementById('modal-overlay').classList.add('visible');
    }

    function cerrarModal() {
        document.getElementById('modal-overlay').classList.remove('visible');
        reporteActualId = null;
    }

    document.getElementById('modal-overlay').addEventListener('click', function(e) {
        if (e.target === this) cerrarModal();
    });

    async function guardarCambios() {
        const estado = document.getElementById('modal-estado').value;
        const notas  = document.getElementById('modal-notas').value;

        try {
            const res = await fetch(`/panel-autoridad/${reporteActualId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ estado, notas_autoridad: notas }),
            });

            if (res.ok) {
                document.getElementById('alerta-ok').style.display  = 'block';
                document.getElementById('alerta-err').style.display = 'none';
                setTimeout(() => {
                    cerrarModal();
                    window.location.reload();
                }, 1200);
            } else {
                document.getElementById('alerta-err').style.display = 'block';
            }
        } catch (e) {
            document.getElementById('alerta-err').style.display = 'block';
        }
    }
</script>
@endpush