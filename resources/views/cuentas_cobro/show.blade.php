@extends('layouts.app')

@section('title', 'Detalle de Cuenta - Dewey Accounts')

@section('content')
<style>
    /* Wix-inspired Detail View Styles */
    :root {
        --wix-blue: #116dff;
        --wix-dark: #20303c;
        --wix-gray: #f4f4f4;
        --wix-text: #162d3d;
        --wix-border: #eef1f5;
        --wix-success: #10b981;
        --wix-warning: #f59e0b;
        --wix-danger: #ef4444;
    }

    .wix-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* Header */
    .wix-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
    }

    .wix-title h1 {
        font-family: 'Inter', sans-serif;
        font-size: 28px;
        font-weight: 800;
        color: var(--wix-text);
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .wix-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6b7c93;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: color 0.2s;
    }

    .wix-back-btn:hover {
        color: var(--wix-blue);
    }

    /* Main Grid */
    .wix-detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 32px;
    }

    /* Cards */
    .wix-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid var(--wix-border);
        padding: 32px;
        margin-bottom: 24px;
    }

    .wix-card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--wix-text);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--wix-border);
    }

    .wix-card-title .material-symbols-rounded {
        color: var(--wix-blue);
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .info-item label {
        display: block;
        font-size: 12px;
        color: #8795a1;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .info-item div {
        font-size: 15px;
        color: var(--wix-text);
        font-weight: 500;
    }

    /* Summary Banner */
    .summary-banner {
        background: linear-gradient(135deg, var(--wix-dark) 0%, #2c3e50 100%);
        color: white;
        border-radius: 12px;
        padding: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        box-shadow: 0 10px 30px rgba(32, 48, 60, 0.15);
    }

    .summary-info h2 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .summary-info p {
        opacity: 0.7;
        font-size: 14px;
    }

    .summary-total {
        text-align: right;
    }

    .summary-total label {
        display: block;
        font-size: 12px;
        opacity: 0.7;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .summary-total span {
        font-size: 36px;
        font-weight: 700;
        color: #4ade80; /* Bright green for money */
    }

    /* Status Badge */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 32px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #eef1f5;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 24px;
    }

    .timeline-dot {
        position: absolute;
        left: -32px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        border: 3px solid var(--wix-blue);
        z-index: 1;
    }

    .timeline-content {
        background: #f9fafb;
        border-radius: 8px;
        padding: 16px;
        border: 1px solid var(--wix-border);
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .timeline-action {
        font-weight: 700;
        color: var(--wix-text);
    }

    .timeline-date {
        font-size: 12px;
        color: #8795a1;
    }

    /* Buttons */
    .wix-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        width: 100%;
        justify-content: center;
        margin-bottom: 12px;
    }

    .wix-btn-primary {
        background: var(--wix-blue);
        color: white;
    }
    .wix-btn-primary:hover { background: #0056d6; }

    .wix-btn-success {
        background: var(--wix-success);
        color: white;
    }
    .wix-btn-success:hover { background: #059669; }

    .wix-btn-danger {
        background: var(--wix-danger);
        color: white;
    }
    .wix-btn-danger:hover { background: #dc2626; }

    .wix-btn-secondary {
        background: white;
        border: 1px solid #d1d5db;
        color: var(--wix-text);
    }
    .wix-btn-secondary:hover { background: #f9fafb; }

    /* Items Table */
    .wix-items-table {
        width: 100%;
        border-collapse: collapse;
    }
    .wix-items-table th {
        text-align: left;
        padding: 12px;
        font-size: 12px;
        color: #8795a1;
        text-transform: uppercase;
        border-bottom: 1px solid var(--wix-border);
    }
    .wix-items-table td {
        padding: 16px 12px;
        border-bottom: 1px solid var(--wix-border);
        color: var(--wix-text);
    }
    .text-right { text-align: right; }

    @media (max-width: 900px) {
        .wix-detail-grid { grid-template-columns: 1fr; }
        .summary-banner { flex-direction: column; align-items: flex-start; gap: 20px; }
        .summary-total { text-align: left; }
    }
</style>

@php
    $authUser = auth()->user();
    $userRole = $authUser?->role?->name ?? 'guest';
    $isContratistaOwner = $cuenta->isOwner($authUser);
    $canApprove = $cuenta->canUserApprove($authUser);
    $canSendClient = $cuenta->canSendToClient($authUser);
@endphp

<div class="wix-container">
    <div class="wix-header">
        <div class="wix-title">
            <a href="{{ route('cuentas_cobro.index') }}" class="wix-back-btn">
                <span class="material-symbols-rounded">arrow_back</span>
                Volver
            </a>
            <h1>
                Cuenta de Cobro #{{ $cuenta->id }}
            </h1>
        </div>
        <div>
            @php
                $colors = [
                    'pendiente' => '#f59e0b',
                    'en_revision' => '#3b82f6',
                    'aprobado' => '#10b981',
                    'rechazado' => '#ef4444',
                    'pagado' => '#059669'
                ];
                $color = $colors[$cuenta->estado_aprobacion] ?? '#6b7c93';
            @endphp
            <span class="status-pill" style="background: {{ $color }}20; color: {{ $color }};">
                <span class="material-symbols-rounded" style="font-size: 18px;">
                    {{ $cuenta->estado_aprobacion === 'aprobado' ? 'check_circle' : 'pending' }}
                </span>
                {{ ucfirst(str_replace('_', ' ', $cuenta->estado_aprobacion)) }}
            </span>
        </div>
    </div>

    <div class="summary-banner">
        <div class="summary-info">
            <h2>{{ $cuenta->numero ?? 'Borrador' }}</h2>
            <p>Emitida el {{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d F, Y') }}</p>
        </div>
        <div class="summary-total">
            <label>Valor Total</label>
            <span>${{ number_format($cuenta->valor_total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="wix-detail-grid">
        <!-- Left Column: Details -->
        <div class="detail-left">
            
            <!-- Beneficiary Info -->
            <div class="wix-card">
                <h3 class="wix-card-title">
                    <span class="material-symbols-rounded">person</span>
                    Información del Beneficiario
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nombre</label>
                        <div>{{ $cuenta->nombre_beneficiario }}</div>
                    </div>
                    <div class="info-item">
                        <label>Identificación</label>
                        <div>{{ $cuenta->tipo_identificacion }} {{ $cuenta->identificacion }}</div>
                    </div>
                    <div class="info-item">
                        <label>Tipo Cliente</label>
                        <div>{{ ucfirst($cuenta->tipo_cliente) }}</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Sections (Legal Panels) -->
            @include('cuentas_cobro.partials.details-grid', ['visibleSections' => $visibleSections ?? []])

            <!-- Items -->
            @if($cuenta->items && $cuenta->items->count() > 0)
            <div class="wix-card">
                <h3 class="wix-card-title">
                    <span class="material-symbols-rounded">inventory_2</span>
                    Ítems
                </h3>
                <table class="wix-items-table">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th class="text-right">Cant.</th>
                            <th class="text-right">Unitario</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuenta->items as $item)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $item->item }}</div>
                                <div style="font-size: 13px; color: #8795a1;">{{ $item->detalle }}</div>
                            </td>
                            <td class="text-right">{{ $item->cantidad }}</td>
                            <td class="text-right">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                            <td class="text-right" style="font-weight: 600;">${{ number_format($item->cantidad * $item->precio_unitario, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Soportes -->
            <div class="wix-card">
                <h3 class="wix-card-title">
                    <span class="material-symbols-rounded">attach_file</span>
                    Soportes Adjuntos
                </h3>
                @if($cuenta->soportes && $cuenta->soportes->count() > 0)
                    <div style="display: grid; gap: 12px;">
                        @foreach($cuenta->soportes as $soporte)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f9fafb; border-radius: 8px; border: 1px solid var(--wix-border);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="material-symbols-rounded" style="color: var(--wix-blue);">description</span>
                                    <a href="{{ Storage::url($soporte->path) }}" target="_blank" style="color: var(--wix-text); text-decoration: none; font-weight: 500;">
                                        {{ $soporte->nombre }}
                                    </a>
                                </div>
                                @if($isContratistaOwner && in_array($cuenta->estado_aprobacion, ['en_correccion','en_revision']))
                                    <form action="{{ route('cuentas_cobro.soportes.destroy', [$cuenta->id, $soporte->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar soporte?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer;">
                                            <span class="material-symbols-rounded">delete</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #8795a1; font-style: italic;">No hay soportes adjuntos.</p>
                @endif

                @if($isContratistaOwner && in_array($cuenta->estado_aprobacion, ['en_correccion','en_revision']))
                    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px dashed var(--wix-border);">
                        <form action="{{ route('cuentas_cobro.soportes.store', $cuenta->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label style="display: block; margin-bottom: 16px; font-weight: 700; color: var(--wix-text); font-size: 16px;">
                                <span class="material-symbols-rounded" style="vertical-align: bottom; margin-right: 6px; color: var(--wix-blue);">cloud_upload</span>
                                Subir nuevo soporte
                            </label>
                            
                            <div class="upload-area" id="uploadArea">
                                <input type="file" name="soportes[]" id="fileInput" multiple required class="file-input-hidden" onchange="updateFileName(this)">
                                <label for="fileInput" class="upload-label">
                                    <div class="upload-icon-circle">
                                        <span class="material-symbols-rounded">upload_file</span>
                                    </div>
                                    <span class="upload-text">Haz clic para seleccionar archivos</span>
                                    <span class="upload-hint" id="fileNameDisplay">o arrastra y suelta tus documentos aquí</span>
                                </label>
                            </div>

                            <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
                                <button type="submit" class="wix-btn wix-btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                                    <span class="material-symbols-rounded">add_circle</span>
                                    Adjuntar Archivos Seleccionados
                                </button>
                            </div>
                        </form>
                    </div>

                    <style>
                        .upload-area {
                            border: 2px dashed #cfd4da;
                            border-radius: 12px;
                            background: #f8fafc;
                            transition: all 0.2s ease;
                            position: relative;
                            overflow: hidden;
                        }
                        .upload-area:hover {
                            border-color: var(--wix-blue);
                            background: #f0f7ff;
                        }
                        .file-input-hidden {
                            position: absolute;
                            width: 100%;
                            height: 100%;
                            opacity: 0;
                            cursor: pointer;
                            z-index: 2;
                        }
                        .upload-label {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            padding: 40px 24px;
                            cursor: pointer;
                            text-align: center;
                        }
                        .upload-icon-circle {
                            width: 48px;
                            height: 48px;
                            background: #e0e7ff;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: 12px;
                            color: var(--wix-blue);
                        }
                        .upload-icon-circle .material-symbols-rounded {
                            font-size: 24px;
                        }
                        .upload-text {
                            font-weight: 600;
                            color: var(--wix-text);
                            font-size: 15px;
                            margin-bottom: 4px;
                        }
                        .upload-hint {
                            font-size: 13px;
                            color: var(--wix-text-muted);
                            margin-top: 4px;
                        }
                    </style>

                    <script>
                        function updateFileName(input) {
                            const display = document.getElementById('fileNameDisplay');
                            if (input.files && input.files.length > 0) {
                                if (input.files.length === 1) {
                                    display.textContent = input.files[0].name;
                                    display.style.color = 'var(--wix-primary)';
                                    display.style.fontWeight = '600';
                                } else {
                                    display.textContent = input.files.length + ' archivos seleccionados';
                                    display.style.color = 'var(--wix-primary)';
                                    display.style.fontWeight = '600';
                                }
                            } else {
                                display.textContent = 'o arrastra y suelta aquí';
                                display.style.color = 'var(--wix-text-muted)';
                                display.style.fontWeight = 'normal';
                            }
                        }
                    </script>
                @endif
            </div>

        </div>

        <!-- Right Column: Actions & Timeline -->
        <div class="detail-right">
            
            <!-- Actions Card -->
            <div class="wix-card">
                <h3 class="wix-card-title">Acciones</h3>

                <a href="{{ route('cuentas_cobro.historial', $cuenta->id) }}" class="wix-btn wix-btn-secondary" style="margin-bottom: 12px; width: 100%; justify-content: center;">
                    <span class="material-symbols-rounded">history</span> Ver Historial Completo
                </a>

                <a href="{{ route('cuentas_cobro.seguimiento', $cuenta->id) }}" class="wix-btn wix-btn-secondary" style="margin-bottom: 12px; width: 100%; justify-content: center;">
                    <span class="material-symbols-rounded">timeline</span> Ver Seguimiento Detallado
                </a>
                
                @if($isContratistaOwner && $cuenta->estado_aprobacion === 'en_correccion')
                    <a href="{{ route('cuentas_cobro.edit', $cuenta) }}" class="wix-btn wix-btn-secondary">
                        <span class="material-symbols-rounded">edit</span> Editar Cuenta
                    </a>
                    <form action="{{ route('cuentas_cobro.reenviar', $cuenta->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="wix-btn wix-btn-primary">
                            <span class="material-symbols-rounded">send</span> Reenviar a Revisión
                        </button>
                    </form>
                @endif

                @if($canApprove)
                    <button type="button" onclick="document.getElementById('approveModal').style.display='flex'" class="wix-btn wix-btn-success" style="margin-bottom: 12px;">
                        <span class="material-symbols-rounded">check_circle</span> Aprobar
                    </button>
                    <button onclick="document.getElementById('rejectModal').style.display='flex'" class="wix-btn wix-btn-danger">
                        <span class="material-symbols-rounded">cancel</span> Rechazar
                    </button>
                @endif

                {{-- Botones de Devolver y Anular (solo para roles autorizados) --}}
                @if($cuenta->estado_aprobacion !== 'anulado' && (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_programa') || auth()->user()->hasRole('administrador') || auth()->user()->hasRole('tesoreria')))
                    <hr style="margin: 16px 0; border: none; border-top: 1px solid #e5e7eb;">
                    
                    <button type="button" onclick="document.getElementById('devolverGeneralModal').style.display='flex'" class="wix-btn" style="background: #FEF3C7; color: #92400E; margin-bottom: 8px;">
                        <span class="material-symbols-rounded">undo</span> Devolver para Ajuste
                    </button>
                    
                    @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_programa'))
                    <button type="button" onclick="document.getElementById('anularModal').style.display='flex'" class="wix-btn" style="background: #FEE2E2; color: #991B1B;">
                        <span class="material-symbols-rounded">block</span> Anular Cuenta
                    </button>
                    @endif
                @endif

                <a href="{{ route('cuentas_cobro.pdf', $cuenta->id) }}" target="_blank" class="wix-btn wix-btn-secondary">
                    <span class="material-symbols-rounded">picture_as_pdf</span> Descargar PDF
                </a>
            </div>

            <!-- Timeline -->
            <div class="wix-card">
                <h3 class="wix-card-title" style="display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-rounded" style="color: var(--primary);">history</span>
                    Historial de Actividad
                </h3>
                <div class="timeline" style="position: relative; padding-left: 12px; border-left: 2px solid #e2e8f0; margin-left: 8px;">
                    @forelse($cuenta->historial->sortByDesc('created_at') ?? [] as $evento)
                        @php
                            $icon = 'circle';
                            $color = '#94a3b8';
                            $bg = '#f1f5f9';
                            
                            switch($evento->accion) {
                                case 'creado':
                                    $icon = 'add_circle'; $color = '#3b82f6'; $bg = '#eff6ff'; break;
                                case 'enviado':
                                case 'reenviado':
                                    $icon = 'send'; $color = '#6366f1'; $bg = '#e0e7ff'; break;
                                case 'revisado':
                                    $icon = 'rate_review'; $color = '#8b5cf6'; $bg = '#f3e8ff'; break;
                                case 'aprobado':
                                    $icon = 'check_circle'; $color = '#22c55e'; $bg = '#dcfce7'; break;
                                case 'rechazado':
                                    $icon = 'cancel'; $color = '#ef4444'; $bg = '#fee2e2'; break;
                                case 'devuelto':
                                case 'corregido':
                                    $icon = 'build'; $color = '#f97316'; $bg = '#ffedd5'; break;
                                case 'pagado':
                                    $icon = 'paid'; $color = '#10b981'; $bg = '#d1fae5'; break;
                                case 'enviado_cliente':
                                    $icon = 'mark_email_read'; $color = '#0ea5e9'; $bg = '#e0f2fe'; break;
                                default:
                                    $icon = 'info'; $color = '#64748b'; $bg = '#f8fafc'; break;
                            }
                        @endphp
                        <div class="timeline-item" style="position: relative; margin-bottom: 24px; padding-left: 24px;">
                            <!-- Icon Dot -->
                            <div style="position: absolute; left: -21px; top: 0; width: 36px; height: 36px; background: {{ $bg }}; border: 2px solid white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: {{ $color }}; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <span class="material-symbols-rounded" style="font-size: 20px;">{{ $icon }}</span>
                            </div>
                            
                            <div class="timeline-content">
                                <div class="timeline-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                    <span class="timeline-action" style="font-weight: 600; color: var(--wix-text); font-size: 14px;">
                                        {{ ucfirst(str_replace('_', ' ', $evento->accion)) }}
                                    </span>
                                    <span class="timeline-date" style="font-size: 12px; color: #94a3b8; background: #f8fafc; padding: 2px 8px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                        {{ $evento->created_at->format('d M, H:i') }}
                                    </span>
                                </div>
                                
                                <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">
                                    <span style="font-weight: 500;">{{ $evento->user->name ?? 'Sistema' }}</span>
                                    @if($evento->user && $evento->user->role)
                                        <span style="font-size: 11px; color: #94a3b8;">({{ $evento->user->role->name }})</span>
                                    @endif
                                </div>

                                @if($evento->comentario)
                                    <div style="font-size: 13px; color: #475569; background: #f8fafc; padding: 8px 12px; border-radius: 8px; border-left: 3px solid {{ $color }}; margin-top: 8px; font-style: italic;">
                                        "{{ $evento->comentario }}"
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="padding: 16px; text-align: center; color: #94a3b8; font-style: italic;">
                            No hay historial registrado.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Interacciones -->
            @include('cuentas_cobro.partials.interacciones')

        </div>
    </div>
</div>

<!-- Modals (Simplified for brevity, keeping functionality) -->
<div id="approveModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:32px; width:90%; max-width:400px; text-align:center; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
        <div style="width:60px; height:60px; background:#dcfce7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; color:#16a34a;">
            <span class="material-symbols-rounded" style="font-size:32px;">check_circle</span>
        </div>
        <h3 style="margin-top:0; margin-bottom:8px; color:var(--wix-text); font-size:20px;">¿Aprobar cuenta?</h3>
        <p style="color:#6b7c93; margin-bottom:24px; font-size:15px;">La cuenta avanzará a la siguiente etapa del flujo.</p>
        
        <div style="display:flex; gap:12px; justify-content:center;">
            <button type="button" onclick="document.getElementById('approveModal').style.display='none'" class="wix-btn wix-btn-secondary" style="width:auto;">Cancelar</button>
            <form action="{{ route('cuentas_cobro.aprobar', $cuenta->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="wix-btn wix-btn-success" style="width:auto;">Confirmar Aprobación</button>
            </form>
        </div>
    </div>
</div>

<div id="rejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:32px; width:90%; max-width:500px;">
        <h3 style="margin-top:0; margin-bottom:16px;">Rechazar Cuenta</h3>
        <form action="{{ route('cuentas_cobro.rechazar', $cuenta->id) }}" method="POST">
            @csrf
            <textarea name="motivo_rechazo" rows="4" placeholder="Motivo del rechazo..." required style="width:100%; padding:12px; border:1px solid #eef1f5; border-radius:8px; margin-bottom:16px; font-family:inherit;"></textarea>
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('rejectModal').style.display='none'" class="wix-btn wix-btn-secondary" style="width:auto; margin:0;">Cancelar</button>
                <button type="submit" class="wix-btn wix-btn-danger" style="width:auto; margin:0;">Confirmar Rechazo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Devolver General -->
<div id="devolverGeneralModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:32px; width:90%; max-width:500px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
            <div style="width:48px; height:48px; background:#FEF3C7; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#92400E;">
                <span class="material-symbols-rounded" style="font-size:24px;">undo</span>
            </div>
            <div>
                <h3 style="margin:0; font-size:18px; color:#1a1a2e;">Devolver Cuenta para Ajuste</h3>
                <p style="margin:4px 0 0; font-size:13px; color:#6b7280;">La cuenta será devuelta para modificaciones</p>
            </div>
        </div>
        
        <form action="{{ route('cuentas_cobro.devolver_general', $cuenta->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:600; margin-bottom:6px; color:#374151;">Devolver a</label>
                <select name="devolver_a" required style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px;">
                    <option value="auxiliar">Auxiliar (creador original)</option>
                    <option value="administrador">Administrador</option>
                    <option value="tesoreria">Tesorería</option>
                </select>
            </div>
            
            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:600; margin-bottom:6px; color:#374151;">Motivo de la devolución *</label>
                <textarea name="motivo" rows="4" placeholder="Explique por qué se devuelve esta cuenta (ajuste de plazo, corrección de monto, etc.)..." required minlength="5" style="width:100%; padding:12px; border:1px solid #d1d5db; border-radius:8px; font-family:inherit; font-size:14px;"></textarea>
            </div>
            
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('devolverGeneralModal').style.display='none'" class="wix-btn wix-btn-secondary" style="width:auto; margin:0;">Cancelar</button>
                <button type="submit" class="wix-btn" style="width:auto; margin:0; background:#FF9500; color:white;">
                    <span class="material-symbols-rounded">undo</span> Devolver Cuenta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Anular Cuenta -->
<div id="anularModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:32px; width:90%; max-width:500px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
            <div style="width:48px; height:48px; background:#FEE2E2; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#991B1B;">
                <span class="material-symbols-rounded" style="font-size:24px;">block</span>
            </div>
            <div>
                <h3 style="margin:0; font-size:18px; color:#1a1a2e;">Anular Cuenta de Cobro</h3>
                <p style="margin:4px 0 0; font-size:13px; color:#991B1B;">⚠️ Esta acción no se puede deshacer</p>
            </div>
        </div>
        
        <div style="background:#FEE2E2; padding:12px; border-radius:8px; margin-bottom:16px;">
            <p style="margin:0; font-size:13px; color:#991B1B;">
                <strong>Atención:</strong> Anular una cuenta la marcará permanentemente como inválida y será archivada. Use esta opción solo cuando la cuenta tenga errores irreparables o deba ser reemplazada por una nueva.
            </p>
        </div>
        
        <form action="{{ route('cuentas_cobro.anular', $cuenta->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:600; margin-bottom:6px; color:#374151;">Motivo de la anulación *</label>
                <textarea name="motivo_anulacion" rows="4" placeholder="Explique detalladamente por qué se anula esta cuenta..." required minlength="10" style="width:100%; padding:12px; border:1px solid #d1d5db; border-radius:8px; font-family:inherit; font-size:14px;"></textarea>
            </div>
            
            <div style="margin-bottom:16px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" required style="width:18px; height:18px;">
                    <span style="font-size:14px; color:#374151;">Confirmo que deseo anular esta cuenta de cobro</span>
                </label>
            </div>
            
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('anularModal').style.display='none'" class="wix-btn wix-btn-secondary" style="width:auto; margin:0;">Cancelar</button>
                <button type="submit" class="wix-btn wix-btn-danger" style="width:auto; margin:0;">
                    <span class="material-symbols-rounded">block</span> Anular Cuenta
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
