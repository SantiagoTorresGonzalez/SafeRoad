@extends('layouts.saferoad')

@section('title', 'Panel Autoridad Municipal — SafeRoad SC')

@section('content')
<style>
    :root {
        --sr-verde:       #0d6e4f;
        --sr-verde-med:   #15803d;
        --sr-verde-claro: #dcfce7;
        --sr-azul:        #1e3a5f;
        --sr-azul-med:    #1d4ed8;
        --sr-bg:          #f0f4f8;
        --sr-card:        #ffffff;
        --sr-border:      #e2e8f0;
        --sr-text:        #1e293b;
        --sr-muted:       #64748b;
        --radius:         10px;
    }

    /* ── Layout ── */
    .pa-wrap {
        min-height: 100vh;
        background: var(--sr-bg);
        font-family: 'Segoe UI', system-ui, sans-serif;
        padding-bottom: 3rem;
    }

    /* ── Header ── */
    .pa-header {
        background: linear-gradient(135deg, var(--sr-azul) 0%, var(--sr-verde) 100%);
        color: #fff;
        padding: 2rem 2.5rem 1.6rem;
        position: relative;
        overflow: hidden;
    }
    .pa-header::after {
        content: '';
        position: absolute;
        right: -60px; top: -60px;
        width: 260px; height: 260px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        pointer-events: none;
    }
    .pa-header-inner {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }
    .pa-header-left h1 {
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: -.3px;
        margin: 0 0 .25rem;
    }
    .pa-header-left p {
        font-size: .9rem;
        opacity: .8;
        margin: 0;
    }
    .pa-header-badge {
        background: rgba(255,255,255,.18);
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 999px;
        padding: .35rem 1rem;
        font-size: .8rem;
        font-weight: 600;
        letter-spacing: .4px;
        white-space: nowrap;
    }

    /* ── Stats bar ── */
    .pa-stats {
        background: var(--sr-card);
        border-bottom: 1px solid var(--sr-border);
    }
    .pa-stats-inner {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        flex-wrap: wrap;
    }
    .pa-stat {
        flex: 1;
        min-width: 130px;
        padding: 1.1rem 1.5rem;
        border-right: 1px solid var(--sr-border);
        display: flex;
        flex-direction: column;
        gap: .2rem;
    }
    .pa-stat:last-child { border-right: none; }
    .pa-stat-label {
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .6px;
        text-transform: uppercase;
        color: var(--sr-muted);
    }
    .pa-stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
    }
    .pa-stat-value.pendiente { color: #b45309; }
    .pa-stat-value.validado  { color: var(--sr-verde); }
    .pa-stat-value.atencion  { color: var(--sr-azul-med); }
    .pa-stat-value.resuelto  { color: #6b7280; }
    .pa-stat-value.total     { color: var(--sr-azul); }

    /* ── Main content ── */
    .pa-main {
        max-width: 1280px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    /* ── Alert flash ── */
    .pa-flash {
        border-radius: var(--radius);
        padding: .85rem 1.25rem;
        margin-bottom: 1.4rem;
        font-size: .9rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .pa-flash.success {
        background: var(--sr-verde-claro);
        color: var(--sr-verde);
        border: 1px solid #86efac;
    }
    .pa-flash.error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    /* ── Filter card ── */
    .pa-filters {
        background: var(--sr-card);
        border: 1px solid var(--sr-border);
        border-radius: var(--radius);
        padding: 1.2rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: .9rem;
        align-items: flex-end;
    }
    .pa-filters-title {
        width: 100%;
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
        color: var(--sr-muted);
        margin-bottom: .1rem;
    }
    .pa-filter-group {
        display: flex;
        flex-direction: column;
        gap: .35rem;
        flex: 1;
        min-width: 160px;
    }
    .pa-filter-group label {
        font-size: .78rem;
        font-weight: 600;
        color: var(--sr-text);
    }
    .pa-filter-group select,
    .pa-filter-group input {
        border: 1.5px solid var(--sr-border);
        border-radius: 6px;
        padding: .5rem .75rem;
        font-size: .875rem;
        color: var(--sr-text);
        background: #f8fafc;
        transition: border-color .15s;
        outline: none;
    }
    .pa-filter-group select:focus,
    .pa-filter-group input:focus {
        border-color: var(--sr-verde);
        background: #fff;
    }
    .pa-btn {
        padding: .5rem 1.25rem;
        border-radius: 6px;
        font-size: .875rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        text-decoration: none;
    }
    .pa-btn:hover { opacity: .88; transform: translateY(-1px); }
    .pa-btn:active { transform: translateY(0); }
    .pa-btn-primary { background: var(--sr-verde); color: #fff; }
    .pa-btn-outline { background: transparent; border: 1.5px solid var(--sr-border); color: var(--sr-text); }
    .pa-btn-sm { padding: .35rem .85rem; font-size: .8rem; }
    .pa-btn-validar   { background: var(--sr-verde); color: #fff; }
    .pa-btn-atencion  { background: var(--sr-azul-med); color: #fff; }
    .pa-btn-descartar { background: #dc2626; color: #fff; }

    /* ── Table card ── */
    .pa-table-card {
        background: var(--sr-card);
        border: 1px solid var(--sr-border);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .pa-table-head {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--sr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
    }
    .pa-table-head h2 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--sr-text);
        margin: 0;
    }
    .pa-count-badge {
        background: var(--sr-verde-claro);
        color: var(--sr-verde);
        border-radius: 999px;
        padding: .2rem .75rem;
        font-size: .78rem;
        font-weight: 700;
    }

    /* ── Responsive table wrapper ── */
    .pa-table-wrap { overflow-x: auto; }
    table.pa-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }
    .pa-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid var(--sr-border);
    }
    .pa-table th {
        padding: .75rem 1rem;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: var(--sr-muted);
        text-align: left;
        white-space: nowrap;
    }
    .pa-table td {
        padding: .85rem 1rem;
        border-bottom: 1px solid var(--sr-border);
        vertical-align: middle;
        color: var(--sr-text);
    }
    .pa-table tbody tr:hover { background: #f8fafc; }
    .pa-table tbody tr:last-child td { border-bottom: none; }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        border-radius: 999px;
        padding: .2rem .7rem;
        font-size: .75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge::before { content: '●'; font-size: .55rem; }
    .badge-pendiente  { background: #fef3c7; color: #92400e; }
    .badge-validado   { background: var(--sr-verde-claro); color: #065f46; }
    .badge-en_atencion { background: #dbeafe; color: #1e40af; }
    .badge-resuelto   { background: #f1f5f9; color: #475569; }
    .badge-descartado { background: #fee2e2; color: #991b1b; }

    /* ── Tipo riesgo tag ── */
    .tipo-tag {
        background: #f1f5f9;
        color: var(--sr-azul);
        border-radius: 4px;
        padding: .18rem .55rem;
        font-size: .75rem;
        font-weight: 600;
    }

    /* ── Actions cell ── */
    .actions-cell {
        display: flex;
        gap: .4rem;
        flex-wrap: wrap;
    }

    /* ── Desc truncate ── */
    .desc-cell {
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── Pagination ── */
    .pa-pagination {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--sr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
        font-size: .83rem;
        color: var(--sr-muted);
    }
    .pa-pagination .links a,
    .pa-pagination .links span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px; height: 32px;
        border-radius: 6px;
        font-size: .82rem;
        font-weight: 600;
        color: var(--sr-text);
        text-decoration: none;
        border: 1.5px solid var(--sr-border);
        margin: 0 1px;
        transition: all .15s;
    }
    .pa-pagination .links a:hover { border-color: var(--sr-verde); color: var(--sr-verde); }
    .pa-pagination .links span.active {
        background: var(--sr-verde); color: #fff; border-color: var(--sr-verde);
    }

    /* ── Modal overlay ── */
    .sr-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.55);
        backdrop-filter: blur(3px);
        z-index: 9000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .sr-modal-overlay.open { display: flex; }
    .sr-modal {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 25px 60px rgba(0,0,0,.25);
        overflow: hidden;
        animation: modalIn .22s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes modalIn {
        from { transform: scale(.92) translateY(12px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }
    .sr-modal-header {
        padding: 1.25rem 1.5rem 1rem;
        border-bottom: 1px solid var(--sr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sr-modal-header h3 { margin: 0; font-size: 1.05rem; font-weight: 700; }
    .sr-modal-close {
        background: none; border: none; cursor: pointer;
        font-size: 1.3rem; color: var(--sr-muted); padding: .2rem;
        border-radius: 4px; line-height: 1;
        transition: color .15s;
    }
    .sr-modal-close:hover { color: var(--sr-text); }
    .sr-modal-body { padding: 1.25rem 1.5rem; }
    .sr-modal-footer {
        padding: 1rem 1.5rem 1.25rem;
        border-top: 1px solid var(--sr-border);
        display: flex;
        justify-content: flex-end;
        gap: .6rem;
    }
    .sr-modal-info {
        background: #f8fafc;
        border-radius: 8px;
        padding: .85rem 1rem;
        margin-bottom: 1rem;
        font-size: .875rem;
    }
    .sr-modal-info p { margin: .2rem 0; color: var(--sr-muted); }
    .sr-modal-info strong { color: var(--sr-text); }
    .sr-modal-label {
        font-size: .8rem;
        font-weight: 700;
        color: var(--sr-text);
        margin-bottom: .4rem;
        display: block;
    }
    .sr-modal-textarea {
        width: 100%;
        border: 1.5px solid var(--sr-border);
        border-radius: 7px;
        padding: .65rem .9rem;
        font-size: .875rem;
        font-family: inherit;
        resize: vertical;
        min-height: 90px;
        outline: none;
        transition: border-color .15s;
        box-sizing: border-box;
    }
    .sr-modal-textarea:focus { border-color: var(--sr-verde); }

    /* ── Empty state ── */
    .pa-empty {
        text-align: center;
        padding: 3.5rem 1rem;
        color: var(--sr-muted);
    }
    .pa-empty svg { opacity: .35; margin-bottom: 1rem; }
    .pa-empty p { font-size: .95rem; }

    /* ── Foto thumbnail ── */
    .foto-thumb {
        width: 44px; height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--sr-border);
        cursor: pointer;
        transition: transform .15s;
    }
    .foto-thumb:hover { transform: scale(1.08); }

    @media (max-width: 768px) {
        .pa-header { padding: 1.4rem 1.2rem 1.1rem; }
        .pa-header-left h1 { font-size: 1.25rem; }
        .pa-main { padding: 0 .75rem; }
        .pa-filters { padding: 1rem; }
        .pa-table th, .pa-table td { padding: .65rem .7rem; }
    }
</style>

<div class="pa-wrap">

    {{-- ── Header ── --}}
    <div class="pa-header">
        <div class="pa-header-inner">
            <div class="pa-header-left">
                <h1>
                    <svg style="display:inline;vertical-align:-.15em;margin-right:.4rem" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Panel Autoridad Municipal
                </h1>
                <p>Gestión y validación de reportes ciudadanos — SafeRoad SC</p>
            </div>
            <span class="pa-header-badge">
                🏛 {{ auth()->user()->name ?? 'Autoridad' }}
            </span>
        </div>
    </div>

    {{-- ── Stats bar ── --}}
    <div class="pa-stats">
        <div class="pa-stats-inner">
            <div class="pa-stat">
                <span class="pa-stat-label">Total</span>
                <span class="pa-stat-value total">{{ $stats['total'] }}</span>
            </div>
            <div class="pa-stat">
                <span class="pa-stat-label">Pendientes</span>
                <span class="pa-stat-value pendiente">{{ $stats['pendiente'] }}</span>
            </div>
            <div class="pa-stat">
                <span class="pa-stat-label">Validados</span>
                <span class="pa-stat-value validado">{{ $stats['validado'] }}</span>
            </div>
            <div class="pa-stat">
                <span class="pa-stat-label">En Atención</span>
                <span class="pa-stat-value atencion">{{ $stats['en_atencion'] }}</span>
            </div>
            <div class="pa-stat">
                <span class="pa-stat-label">Resueltos</span>
                <span class="pa-stat-value resuelto">{{ $stats['resuelto'] }}</span>
            </div>
        </div>
    </div>

    {{-- ── Main ── --}}
    <div class="pa-main">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="pa-flash success">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="pa-flash error">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ── Filters ── --}}
        <form method="GET" action="{{ route('panel.index') }}">
            <div class="pa-filters">
                <span class="pa-filters-title">🔍 Filtros</span>

                <div class="pa-filter-group">
                    <label for="f-municipio">Municipio</label>
                    <select name="municipio" id="f-municipio">
                        <option value="">Todos</option>
                        @foreach($municipios as $m)
                            <option value="{{ $m }}" {{ request('municipio') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pa-filter-group">
                    <label for="f-estado">Estado</label>
                    <select name="estado" id="f-estado">
                        <option value="">Todos</option>
                        <option value="pendiente"   {{ request('estado') === 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                        <option value="validado"    {{ request('estado') === 'validado'    ? 'selected' : '' }}>Validado</option>
                        <option value="en_atencion" {{ request('estado') === 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                        <option value="resuelto"    {{ request('estado') === 'resuelto'    ? 'selected' : '' }}>Resuelto</option>
                        <option value="descartado"  {{ request('estado') === 'descartado'  ? 'selected' : '' }}>Descartado</option>
                    </select>
                </div>

                <div class="pa-filter-group">
                    <label for="f-tipo">Tipo de riesgo</label>
                    <select name="tipo_riesgo" id="f-tipo">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t }}" {{ request('tipo_riesgo') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pa-filter-group">
                    <label for="f-buscar">Buscar descripción</label>
                    <input type="text" name="buscar" id="f-buscar" placeholder="Palabras clave…" value="{{ request('buscar') }}">
                </div>

                <div style="display:flex;gap:.5rem;align-self:flex-end">
                    <button type="submit" class="pa-btn pa-btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Filtrar
                    </button>
                    <a href="{{ route('panel.index') }}" class="pa-btn pa-btn-outline">Limpiar</a>
                </div>
            </div>
        </form>

        {{-- ── Table card ── --}}
        <div class="pa-table-card">
            <div class="pa-table-head">
                <h2>Reportes ciudadanos</h2>
                <span class="pa-count-badge">{{ $reportes->total() }} encontrados</span>
            </div>

            <div class="pa-table-wrap">
                @if($reportes->isEmpty())
                    <div class="pa-empty">
                        <svg width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p>No hay reportes que coincidan con los filtros aplicados.</p>
                    </div>
                @else
                <table class="pa-table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Foto</th>
                            <th>Municipio</th>
                            <th>Tipo de riesgo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Registrado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $r)
                        <tr>
                            <td><strong style="color:var(--sr-azul)">#{{ $r->id }}</strong></td>
                            <td>
                                @if($r->foto)
                                    <img
                                        src="{{ asset('storage/' . $r->foto) }}"
                                        alt="Foto reporte"
                                        class="foto-thumb"
                                        onclick="abrirFoto(this.src)"
                                    >
                                @else
                                    <span style="font-size:.75rem;color:var(--sr-muted)">Sin foto</span>
                                @endif
                            </td>
                            <td>{{ $r->municipio }}</td>
                            <td><span class="tipo-tag">{{ ucfirst(str_replace('_',' ',$r->tipo_riesgo)) }}</span></td>
                            <td class="desc-cell" title="{{ $r->descripcion }}">{{ $r->descripcion }}</td>
                            <td>
                                <span class="badge badge-{{ $r->estado }}">
                                    {{ ucfirst(str_replace('_',' ',$r->estado)) }}
                                </span>
                            </td>
                            <td style="white-space:nowrap;color:var(--sr-muted);font-size:.8rem">
                                {{ $r->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="actions-cell">
                                    {{-- Validar --}}
                                    @if(in_array($r->estado, ['pendiente', 'en_atencion']))
                                    <button
                                        class="pa-btn pa-btn-sm pa-btn-validar"
                                        onclick="abrirModal('validar', {{ $r->id }}, '{{ addslashes($r->municipio) }}', '{{ addslashes($r->tipo_riesgo) }}')"
                                        title="Validar reporte">
                                        ✓ Validar
                                    </button>
                                    @endif

                                    {{-- En Atención --}}
                                    @if(in_array($r->estado, ['pendiente', 'validado']))
                                    <button
                                        class="pa-btn pa-btn-sm pa-btn-atencion"
                                        onclick="abrirModal('atencion', {{ $r->id }}, '{{ addslashes($r->municipio) }}', '{{ addslashes($r->tipo_riesgo) }}')"
                                        title="Marcar en atención">
                                        ⚙ Atención
                                    </button>
                                    @endif

                                    {{-- Descartar --}}
                                    @if(!in_array($r->estado, ['descartado', 'resuelto']))
                                    <button
                                        class="pa-btn pa-btn-sm pa-btn-descartar"
                                        onclick="abrirModal('descartar', {{ $r->id }}, '{{ addslashes($r->municipio) }}', '{{ addslashes($r->tipo_riesgo) }}')"
                                        title="Descartar reporte">
                                        ✕ Descartar
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- Pagination --}}
            @if($reportes->hasPages())
            <div class="pa-pagination">
                <span>
                    Mostrando {{ $reportes->firstItem() }}–{{ $reportes->lastItem() }} de {{ $reportes->total() }} reportes
                </span>
                <div class="links">
                    {{ $reportes->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
            </div>
            @endif
        </div>

    </div>{{-- /pa-main --}}
</div>{{-- /pa-wrap --}}


{{-- ══════════════════════════════════════
     MODAL — Acción sobre reporte
══════════════════════════════════════ --}}
<div class="sr-modal-overlay" id="accionModal">
    <div class="sr-modal" role="dialog" aria-modal="true">
        <div class="sr-modal-header">
            <h3 id="modal-title">Acción sobre reporte</h3>
            <button class="sr-modal-close" onclick="cerrarModal()" aria-label="Cerrar">✕</button>
        </div>
        <div class="sr-modal-body">
            <div class="sr-modal-info">
                <p>Reporte <strong id="modal-id"></strong> — <strong id="modal-municipio"></strong></p>
                <p>Tipo: <strong id="modal-tipo"></strong></p>
                <p id="modal-accion-desc" style="margin-top:.5rem;font-weight:600"></p>
            </div>
            <label class="sr-modal-label" for="modal-notas">Notas de autoridad <span style="font-weight:400;color:var(--sr-muted)">(opcional)</span></label>
            <textarea
                class="sr-modal-textarea"
                id="modal-notas"
                placeholder="Agrega observaciones o justificación de la acción…"
                maxlength="800"
            ></textarea>
        </div>
        <div class="sr-modal-footer">
            <button class="pa-btn pa-btn-outline" onclick="cerrarModal()">Cancelar</button>
            <button class="pa-btn" id="modal-confirm-btn" onclick="confirmarAccion()">Confirmar</button>
        </div>
    </div>
</div>

{{-- Foto lightbox --}}
<div class="sr-modal-overlay" id="fotoModal" onclick="document.getElementById('fotoModal').classList.remove('open')">
    <div style="max-width:90vw;max-height:88vh">
        <img id="foto-full" src="" alt="Foto del reporte"
            style="max-width:90vw;max-height:88vh;border-radius:10px;object-fit:contain">
    </div>
</div>

{{-- Hidden form para enviar la acción --}}
<form id="accion-form" method="POST" style="display:none">
    @csrf
    @method('PATCH')
    <input type="hidden" name="nuevo_estado" id="form-estado">
    <input type="hidden" name="notas_autoridad" id="form-notas">
</form>

<script>
    let modalReporteId = null;
    let modalAccion    = null;

    const accionConfig = {
        validar:  { titulo: 'Validar reporte',       estado: 'validado',    desc: '¿Confirmas que este reporte es válido y requiere atención?', color: '#0d6e4f' },
        atencion: { titulo: 'Marcar en atención',     estado: 'en_atencion', desc: '¿Confirmas que este reporte está siendo atendido?',          color: '#1d4ed8' },
        descartar:{ titulo: 'Descartar reporte',      estado: 'descartado',  desc: '⚠ Esta acción descartará el reporte como inválido o duplicado.', color: '#dc2626' },
    };

    function abrirModal(accion, id, municipio, tipo) {
        const cfg = accionConfig[accion];
        if (!cfg) return;

        modalReporteId = id;
        modalAccion    = accion;

        document.getElementById('modal-title').textContent    = cfg.titulo;
        document.getElementById('modal-id').textContent       = '#' + id;
        document.getElementById('modal-municipio').textContent = municipio;
        document.getElementById('modal-tipo').textContent     = tipo.replace(/_/g,' ');
        document.getElementById('modal-accion-desc').textContent = cfg.desc;
        document.getElementById('modal-notas').value          = '';

        const btn = document.getElementById('modal-confirm-btn');
        btn.textContent = cfg.titulo;
        btn.style.background = cfg.color;
        btn.style.color = '#fff';
        btn.style.border = 'none';

        document.getElementById('accionModal').classList.add('open');
        setTimeout(() => document.getElementById('modal-notas').focus(), 100);
    }

    function cerrarModal() {
        document.getElementById('accionModal').classList.remove('open');
    }

    function confirmarAccion() {
        if (!modalReporteId || !modalAccion) return;
        const cfg = accionConfig[modalAccion];
        const notas = document.getElementById('modal-notas').value.trim();

        const form = document.getElementById('accion-form');
        form.action = '/panel-autoridad/' + modalReporteId;
        document.getElementById('form-estado').value = cfg.estado;
        document.getElementById('form-notas').value  = notas;

        cerrarModal();
        form.submit();
    }

    function abrirFoto(src) {
        document.getElementById('foto-full').src = src;
        document.getElementById('fotoModal').classList.add('open');
    }

    // Cerrar modales con ESC
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            cerrarModal();
            document.getElementById('fotoModal').classList.remove('open');
        }
    });
</script>
@endsection
