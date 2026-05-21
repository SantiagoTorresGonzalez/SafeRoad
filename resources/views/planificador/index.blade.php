@extends('layouts.saferoad')

@section('title', 'Panel Planificador Territorial — SafeRoad SC')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Fraunces:ital,wght@0,300;0,600;0,700;1,300&display=swap');

    :root {
        --sr-verde:       #0d6e4f;
        --sr-verde-med:   #15803d;
        --sr-verde-claro: #dcfce7;
        --sr-azul:        #1e3a5f;
        --sr-azul-med:    #1d4ed8;

        /* Planificador: tono slate/índigo más técnico y cartográfico */
        --pl-accent:      #4f46e5;
        --pl-accent-soft: #eef2ff;
        --pl-dark:        #0f172a;
        --pl-mid:         #334155;
        --pl-muted:       #64748b;
        --pl-border:      #e2e8f0;
        --pl-bg:          #f1f5f9;
        --pl-card:        #ffffff;
        --radius:         10px;
    }

    /* ── Reset & base ── */
    .pl-wrap {
        min-height: 100vh;
        background: var(--pl-bg);
        font-family: 'Fraunces', Georgia, serif;
        padding-bottom: 3rem;
    }

    /* ── Header ── */
    .pl-header {
        background: var(--pl-dark);
        color: #fff;
        padding: 0;
        position: relative;
        overflow: hidden;
    }
    .pl-header-bg {
        position: absolute;
        inset: 0;
        background:
            repeating-linear-gradient(
                0deg,
                transparent,
                transparent 39px,
                rgba(255,255,255,.03) 39px,
                rgba(255,255,255,.03) 40px
            ),
            repeating-linear-gradient(
                90deg,
                transparent,
                transparent 39px,
                rgba(255,255,255,.03) 39px,
                rgba(255,255,255,.03) 40px
            );
    }
    .pl-header-inner {
        position: relative;
        z-index: 1;
        max-width: 1280px;
        margin: 0 auto;
        padding: 2rem 2.5rem 1.8rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.2rem;
    }
    .pl-header-eyebrow {
        font-family: 'DM Mono', monospace;
        font-size: .7rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #818cf8;
        margin-bottom: .5rem;
    }
    .pl-header h1 {
        font-family: 'Fraunces', Georgia, serif;
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 .3rem;
        letter-spacing: -.3px;
        line-height: 1.15;
    }
    .pl-header p {
        font-family: 'DM Mono', monospace;
        font-size: .8rem;
        color: #94a3b8;
        margin: 0;
    }
    .pl-header-user {
        background: rgba(79,70,229,.25);
        border: 1px solid rgba(129,140,248,.3);
        border-radius: 8px;
        padding: .6rem 1.1rem;
        font-family: 'DM Mono', monospace;
        font-size: .75rem;
        color: #c7d2fe;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    /* ── Progress pipeline ── */
    .pl-pipeline {
        background: var(--pl-card);
        border-bottom: 1px solid var(--pl-border);
    }
    .pl-pipeline-inner {
        max-width: 1280px;
        margin: 0 auto;
        padding: 1.1rem 2.5rem;
        display: flex;
        align-items: center;
        gap: 0;
        flex-wrap: wrap;
    }
    .pl-pipe-step {
        display: flex;
        align-items: center;
        gap: .5rem;
        flex: 1;
        min-width: 130px;
        padding: .4rem .6rem;
    }
    .pl-pipe-dot {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'DM Mono', monospace;
        font-size: .75rem;
        font-weight: 500;
        flex-shrink: 0;
    }
    .pl-pipe-dot.validado   { background: var(--sr-verde-claro); color: var(--sr-verde); }
    .pl-pipe-dot.atencion   { background: #dbeafe; color: #1d4ed8; }
    .pl-pipe-dot.resuelto   { background: #f1f5f9; color: #475569; }
    .pl-pipe-label { font-family: 'DM Mono', monospace; font-size: .72rem; }
    .pl-pipe-label span { display: block; font-size: .65rem; color: var(--pl-muted); margin-top: .1rem; }
    .pl-pipe-arrow {
        color: var(--pl-border);
        font-size: 1.1rem;
        flex-shrink: 0;
        padding: 0 .2rem;
    }

    /* ── Main ── */
    .pl-main {
        max-width: 1280px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    /* ── Flash ── */
    .pl-flash {
        border-radius: var(--radius);
        padding: .85rem 1.25rem;
        margin-bottom: 1.4rem;
        font-family: 'DM Mono', monospace;
        font-size: .82rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .pl-flash.success { background: var(--sr-verde-claro); color: var(--sr-verde); border: 1px solid #86efac; }
    .pl-flash.error   { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    /* ── Filters ── */
    .pl-filters {
        background: var(--pl-card);
        border: 1px solid var(--pl-border);
        border-left: 3px solid var(--pl-accent);
        border-radius: var(--radius);
        padding: 1.2rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: .9rem;
        align-items: flex-end;
    }
    .pl-filters-title {
        width: 100%;
        font-family: 'DM Mono', monospace;
        font-size: .68rem;
        font-weight: 500;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--pl-accent);
        margin-bottom: .1rem;
    }
    .pl-filter-group {
        display: flex;
        flex-direction: column;
        gap: .35rem;
        flex: 1;
        min-width: 160px;
    }
    .pl-filter-group label {
        font-family: 'DM Mono', monospace;
        font-size: .72rem;
        color: var(--pl-mid);
        letter-spacing: .3px;
    }
    .pl-filter-group select,
    .pl-filter-group input {
        border: 1.5px solid var(--pl-border);
        border-radius: 6px;
        padding: .5rem .75rem;
        font-size: .875rem;
        font-family: 'DM Mono', monospace;
        color: var(--pl-dark);
        background: #f8fafc;
        outline: none;
        transition: border-color .15s;
    }
    .pl-filter-group select:focus,
    .pl-filter-group input:focus { border-color: var(--pl-accent); background: #fff; }

    .pl-btn {
        padding: .5rem 1.2rem;
        border-radius: 6px;
        font-family: 'DM Mono', monospace;
        font-size: .8rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        text-decoration: none;
        letter-spacing: .3px;
    }
    .pl-btn:hover { opacity: .85; transform: translateY(-1px); }
    .pl-btn:active { transform: translateY(0); }
    .pl-btn-primary { background: var(--pl-accent); color: #fff; }
    .pl-btn-outline  { background: transparent; border: 1.5px solid var(--pl-border); color: var(--pl-mid); }
    .pl-btn-sm { padding: .32rem .8rem; font-size: .75rem; }
    .pl-btn-atencion { background: #1d4ed8; color: #fff; }
    .pl-btn-resolver { background: var(--sr-verde); color: #fff; }

    /* ── Table card ── */
    .pl-table-card {
        background: var(--pl-card);
        border: 1px solid var(--pl-border);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .pl-table-head {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--pl-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
    }
    .pl-table-head h2 {
        font-family: 'Fraunces', serif;
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--pl-dark);
        margin: 0;
    }
    .pl-count {
        background: var(--pl-accent-soft);
        color: var(--pl-accent);
        border-radius: 999px;
        padding: .2rem .75rem;
        font-family: 'DM Mono', monospace;
        font-size: .72rem;
        font-weight: 500;
    }

    .pl-table-wrap { overflow-x: auto; }
    table.pl-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }
    .pl-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid var(--pl-border);
    }
    .pl-table th {
        padding: .75rem 1rem;
        font-family: 'DM Mono', monospace;
        font-size: .67rem;
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--pl-muted);
        text-align: left;
        white-space: nowrap;
    }
    .pl-table td {
        padding: .9rem 1rem;
        border-bottom: 1px solid var(--pl-border);
        vertical-align: middle;
        color: var(--pl-dark);
    }
    .pl-table tbody tr:hover { background: #fafbff; }
    .pl-table tbody tr:last-child td { border-bottom: none; }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        border-radius: 4px;
        padding: .22rem .65rem;
        font-family: 'DM Mono', monospace;
        font-size: .7rem;
        font-weight: 500;
        white-space: nowrap;
        letter-spacing: .3px;
    }
    .badge::before { content: '▸'; font-size: .6rem; }
    .badge-validado    { background: var(--sr-verde-claro); color: #065f46; }
    .badge-en_atencion { background: #dbeafe; color: #1e40af; }
    .badge-resuelto    { background: #f1f5f9; color: #475569; }
    .badge-pendiente   { background: #fef3c7; color: #92400e; }
    .badge-descartado  { background: #fee2e2; color: #991b1b; }

    .tipo-tag {
        background: var(--pl-accent-soft);
        color: var(--pl-accent);
        border-radius: 4px;
        padding: .18rem .55rem;
        font-family: 'DM Mono', monospace;
        font-size: .7rem;
    }

    .actions-cell { display: flex; gap: .4rem; flex-wrap: wrap; }

    .desc-cell {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-family: 'DM Mono', monospace;
        font-size: .78rem;
        color: var(--pl-muted);
    }

    .mono-sm {
        font-family: 'DM Mono', monospace;
        font-size: .75rem;
        color: var(--pl-muted);
        white-space: nowrap;
    }

    .id-chip {
        font-family: 'DM Mono', monospace;
        font-size: .75rem;
        background: var(--pl-accent-soft);
        color: var(--pl-accent);
        border-radius: 4px;
        padding: .15rem .5rem;
    }

    /* ── Pagination ── */
    .pl-pagination {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--pl-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
        font-family: 'DM Mono', monospace;
        font-size: .75rem;
        color: var(--pl-muted);
    }
    .pl-pagination .links a,
    .pl-pagination .links span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px; height: 30px;
        border-radius: 5px;
        font-size: .75rem;
        font-family: 'DM Mono', monospace;
        color: var(--pl-mid);
        text-decoration: none;
        border: 1.5px solid var(--pl-border);
        margin: 0 1px;
        transition: all .15s;
    }
    .pl-pagination .links a:hover { border-color: var(--pl-accent); color: var(--pl-accent); }
    .pl-pagination .links span.active { background: var(--pl-accent); color: #fff; border-color: var(--pl-accent); }

    /* ── Empty ── */
    .pl-empty {
        text-align: center;
        padding: 3.5rem 1rem;
        color: var(--pl-muted);
        font-family: 'DM Mono', monospace;
        font-size: .85rem;
    }

    /* ── Modal ── */
    .pl-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.6);
        backdrop-filter: blur(4px);
        z-index: 9000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .pl-modal-overlay.open { display: flex; }
    .pl-modal {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 30px 70px rgba(0,0,0,.22);
        overflow: hidden;
        animation: modalIn .2s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes modalIn {
        from { transform: scale(.93) translateY(14px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }
    .pl-modal-header {
        padding: 1.25rem 1.5rem 1rem;
        border-bottom: 1px solid var(--pl-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .pl-modal-header h3 {
        margin: 0;
        font-family: 'Fraunces', serif;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--pl-dark);
    }
    .pl-modal-close {
        background: none; border: none; cursor: pointer;
        font-size: 1.2rem; color: var(--pl-muted);
        border-radius: 4px; padding: .2rem;
        transition: color .15s;
    }
    .pl-modal-close:hover { color: var(--pl-dark); }
    .pl-modal-body { padding: 1.25rem 1.5rem; }
    .pl-modal-footer {
        padding: 1rem 1.5rem 1.25rem;
        border-top: 1px solid var(--pl-border);
        display: flex;
        justify-content: flex-end;
        gap: .6rem;
    }

    .pl-modal-info {
        background: var(--pl-accent-soft);
        border-radius: 8px;
        padding: .85rem 1rem;
        margin-bottom: 1rem;
        border-left: 3px solid var(--pl-accent);
    }
    .pl-modal-info p {
        margin: .2rem 0;
        font-family: 'DM Mono', monospace;
        font-size: .78rem;
        color: var(--pl-mid);
    }
    .pl-modal-info strong { color: var(--pl-dark); }

    .pl-modal-label {
        font-family: 'DM Mono', monospace;
        font-size: .75rem;
        font-weight: 500;
        color: var(--pl-dark);
        margin-bottom: .4rem;
        display: block;
        letter-spacing: .3px;
    }
    .pl-modal-textarea {
        width: 100%;
        border: 1.5px solid var(--pl-border);
        border-radius: 7px;
        padding: .65rem .9rem;
        font-size: .875rem;
        font-family: 'DM Mono', monospace;
        resize: vertical;
        min-height: 90px;
        outline: none;
        transition: border-color .15s;
        box-sizing: border-box;
        color: var(--pl-dark);
    }
    .pl-modal-textarea:focus { border-color: var(--pl-accent); }

    .pl-modal-accion-label {
        font-family: 'DM Mono', monospace;
        font-size: .78rem;
        font-weight: 500;
        padding: .3rem .7rem;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: .9rem;
    }
    .pl-modal-accion-label.atencion { background: #dbeafe; color: #1d4ed8; }
    .pl-modal-accion-label.resolver { background: var(--sr-verde-claro); color: var(--sr-verde); }

    @media (max-width: 768px) {
        .pl-header-inner { padding: 1.4rem 1.2rem 1.2rem; }
        .pl-header h1 { font-size: 1.3rem; }
        .pl-main { padding: 0 .75rem; }
        .pl-pipeline-inner { padding: .8rem 1rem; }
    }
</style>

<div class="pl-wrap">

    {{-- ── Header ── --}}
    <div class="pl-header">
        <div class="pl-header-bg"></div>
        <div class="pl-header-inner">
            <div>
                <div class="pl-header-eyebrow">// SafeRoad SC — Módulo de gestión territorial</div>
                <h1>Panel Planificador Territorial</h1>
                <p>Avance de reportes validados → en atención → resueltos</p>
            </div>
            <div class="pl-header-user">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ auth()->user()->name ?? 'Planificador' }}
            </div>
        </div>
    </div>

    {{-- ── Pipeline visual ── --}}
    <div class="pl-pipeline">
        <div class="pl-pipeline-inner">
            <div class="pl-pipe-step">
                <div class="pl-pipe-dot validado">{{ $stats['validado'] }}</div>
                <div class="pl-pipe-label">Validados<span>Listos para atender</span></div>
            </div>
            <div class="pl-pipe-arrow">→</div>
            <div class="pl-pipe-step">
                <div class="pl-pipe-dot atencion">{{ $stats['en_atencion'] }}</div>
                <div class="pl-pipe-label">En Atención<span>Intervención activa</span></div>
            </div>
            <div class="pl-pipe-arrow">→</div>
            <div class="pl-pipe-step">
                <div class="pl-pipe-dot resuelto">{{ $stats['resuelto'] }}</div>
                <div class="pl-pipe-label">Resueltos<span>Cierre confirmado</span></div>
            </div>
            <div style="flex:1"></div>
            <div style="font-family:'DM Mono',monospace;font-size:.72rem;color:var(--pl-muted);white-space:nowrap;align-self:center">
                Total gestionables: <strong style="color:var(--pl-dark)">{{ $stats['total_gestionables'] }}</strong>
            </div>
        </div>
    </div>

    {{-- ── Main ── --}}
    <div class="pl-main">

        {{-- Flash --}}
        @if(session('success'))
            <div class="pl-flash success">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="pl-flash error">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ── Filtros ── --}}
        <form method="GET" action="{{ route('planificador.index') }}">
            <div class="pl-filters">
                <span class="pl-filters-title">◈ Filtros de búsqueda</span>

                <div class="pl-filter-group">
                    <label>Municipio</label>
                    <select name="municipio">
                        <option value="">Todos</option>
                        @foreach($municipios as $m)
                            <option value="{{ $m }}" {{ request('municipio') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pl-filter-group">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos (gestionables)</option>
                        <option value="validado"    {{ request('estado') === 'validado'    ? 'selected' : '' }}>Validado</option>
                        <option value="en_atencion" {{ request('estado') === 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                        <option value="resuelto"    {{ request('estado') === 'resuelto'    ? 'selected' : '' }}>Resuelto</option>
                    </select>
                </div>

                <div class="pl-filter-group">
                    <label>Tipo de riesgo</label>
                    <select name="tipo_riesgo">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t }}" {{ request('tipo_riesgo') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pl-filter-group">
                    <label>Buscar</label>
                    <input type="text" name="buscar" placeholder="Descripción…" value="{{ request('buscar') }}">
                </div>

                <div style="display:flex;gap:.5rem;align-self:flex-end">
                    <button type="submit" class="pl-btn pl-btn-primary">Filtrar</button>
                    <a href="{{ route('planificador.index') }}" class="pl-btn pl-btn-outline">Limpiar</a>
                </div>
            </div>
        </form>

        {{-- ── Tabla ── --}}
        <div class="pl-table-card">
            <div class="pl-table-head">
                <h2>Reportes gestionables</h2>
                <span class="pl-count">{{ $reportes->total() }} registros</span>
            </div>

            <div class="pl-table-wrap">
                @if($reportes->isEmpty())
                    <div class="pl-empty">
                        <div style="font-size:2rem;margin-bottom:.75rem">◎</div>
                        <p>No hay reportes que coincidan con los filtros.</p>
                    </div>
                @else
                <table class="pl-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Municipio</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Notas autoridad</th>
                            <th>Validado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $r)
                        <tr>
                            <td><span class="id-chip">#{{ $r->id }}</span></td>
                            <td class="mono-sm">{{ $r->municipio }}</td>
                            <td><span class="tipo-tag">{{ ucfirst(str_replace('_',' ',$r->tipo_riesgo)) }}</span></td>
                            <td class="desc-cell" title="{{ $r->descripcion }}">{{ $r->descripcion }}</td>
                            <td>
                                <span class="badge badge-{{ $r->estado }}">
                                    {{ ucfirst(str_replace('_',' ',$r->estado)) }}
                                </span>
                            </td>
                            <td class="desc-cell" title="{{ $r->notas_autoridad }}">
                                {{ $r->notas_autoridad ?? '—' }}
                            </td>
                            <td class="mono-sm">
                                {{ $r->validado_at ? \Carbon\Carbon::parse($r->validado_at)->format('d/m/Y') : '—' }}
                            </td>
                            <td>
                                <div class="actions-cell">
                                    {{-- validado → en_atencion --}}
                                    @if($r->estado === 'validado')
                                        <button
                                            class="pl-btn pl-btn-sm pl-btn-atencion"
                                            onclick="abrirModal('atencion', {{ $r->id }}, '{{ addslashes($r->municipio) }}', '{{ addslashes($r->tipo_riesgo) }}', '{{ $r->estado }}')">
                                            ⚙ Atender
                                        </button>
                                    @endif

                                    {{-- en_atencion → resuelto --}}
                                    @if($r->estado === 'en_atencion')
                                        <button
                                            class="pl-btn pl-btn-sm pl-btn-resolver"
                                            onclick="abrirModal('resolver', {{ $r->id }}, '{{ addslashes($r->municipio) }}', '{{ addslashes($r->tipo_riesgo) }}', '{{ $r->estado }}')">
                                            ✓ Resolver
                                        </button>
                                    @endif

                                    @if($r->estado === 'resuelto')
                                        <span class="mono-sm" style="color:#10b981">✓ Cerrado</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            @if($reportes->hasPages())
            <div class="pl-pagination">
                <span>Mostrando {{ $reportes->firstItem() }}–{{ $reportes->lastItem() }} de {{ $reportes->total() }}</span>
                <div class="links">
                    {{ $reportes->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
            </div>
            @endif
        </div>

    </div>
</div>

{{-- ══ MODAL ══ --}}
<div class="pl-modal-overlay" id="accionModal">
    <div class="pl-modal" role="dialog" aria-modal="true">
        <div class="pl-modal-header">
            <h3 id="modal-title">Cambiar estado</h3>
            <button class="pl-modal-close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="pl-modal-body">
            <div class="pl-modal-info">
                <p>Reporte <strong id="modal-id"></strong> — <strong id="modal-municipio"></strong></p>
                <p>Tipo: <strong id="modal-tipo"></strong> &nbsp;|&nbsp; Estado actual: <strong id="modal-estado-actual"></strong></p>
            </div>
            <span class="pl-modal-accion-label" id="modal-accion-label"></span>
            <label class="pl-modal-label" for="modal-notas">
                Evidencia / notas de gestión <span style="font-weight:400;color:var(--pl-muted)">(recomendado)</span>
            </label>
            <textarea
                class="pl-modal-textarea"
                id="modal-notas"
                placeholder="Describe la acción tomada, evidencia adjunta, fechas de intervención…"
                maxlength="800"
            ></textarea>
        </div>
        <div class="pl-modal-footer">
            <button class="pl-btn pl-btn-outline" onclick="cerrarModal()">Cancelar</button>
            <button class="pl-btn" id="modal-confirm-btn" onclick="confirmarAccion()">Confirmar</button>
        </div>
    </div>
</div>

<form id="accion-form" method="POST" style="display:none">
    @csrf
    @method('PATCH')
    <input type="hidden" name="nuevo_estado"    id="form-estado">
    <input type="hidden" name="notas_autoridad" id="form-notas">
</form>

<script>
    let modalId = null;

    const cfg = {
        atencion: {
            titulo:  'Marcar en atención',
            estado:  'en_atencion',
            label:   '⚙ Estado nuevo: EN ATENCIÓN',
            tipo:    'atencion',
            color:   '#1d4ed8',
        },
        resolver: {
            titulo:  'Marcar como resuelto',
            estado:  'resuelto',
            label:   '✓ Estado nuevo: RESUELTO',
            tipo:    'resolver',
            color:   '#0d6e4f',
        },
    };

    function abrirModal(accion, id, municipio, tipo, estadoActual) {
        const c = cfg[accion];
        if (!c) return;
        modalId = { id, accion };

        document.getElementById('modal-title').textContent        = c.titulo;
        document.getElementById('modal-id').textContent           = '#' + id;
        document.getElementById('modal-municipio').textContent    = municipio;
        document.getElementById('modal-tipo').textContent         = tipo.replace(/_/g, ' ');
        document.getElementById('modal-estado-actual').textContent = estadoActual.replace(/_/g, ' ');

        const lbl = document.getElementById('modal-accion-label');
        lbl.textContent = c.label;
        lbl.className = 'pl-modal-accion-label ' + c.tipo;

        document.getElementById('modal-notas').value = '';

        const btn = document.getElementById('modal-confirm-btn');
        btn.textContent = c.titulo;
        btn.style.background = c.color;
        btn.style.color = '#fff';
        btn.style.border = 'none';

        document.getElementById('accionModal').classList.add('open');
        setTimeout(() => document.getElementById('modal-notas').focus(), 80);
    }

    function cerrarModal() {
        document.getElementById('accionModal').classList.remove('open');
        modalId = null;
    }

    function confirmarAccion() {
        if (!modalId) return;
        const c = cfg[modalId.accion];
        const form = document.getElementById('accion-form');
        form.action = '/planificador/' + modalId.id;
        document.getElementById('form-estado').value = c.estado;
        document.getElementById('form-notas').value  = document.getElementById('modal-notas').value.trim();
        cerrarModal();
        form.submit();
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') cerrarModal();
    });
</script>
@endsection
