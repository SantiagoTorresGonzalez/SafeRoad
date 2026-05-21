@extends('layouts.saferoad')

@section('title', 'Panel Analista — SafeRoad SC')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap');

    :root {
        --sr-verde:       #0d6e4f;
        --sr-verde-claro: #dcfce7;
        --sr-azul:        #1e3a5f;

        --an-bg:          #0e1117;
        --an-surface:     #161b27;
        --an-card:        #1c2333;
        --an-border:      #2a3347;
        --an-border-soft: #1e2a3a;
        --an-text:        #e2e8f0;
        --an-muted:       #64748b;
        --an-accent:      #22d3ee;     /* cyan analítico */
        --an-accent-soft: rgba(34,211,238,.1);
        --an-green:       #4ade80;
        --an-amber:       #fbbf24;
        --an-red:         #f87171;
        --an-indigo:      #818cf8;
        --radius:         8px;
    }

    .an-wrap {
        min-height: 100vh;
        background: var(--an-bg);
        font-family: 'IBM Plex Sans', system-ui, sans-serif;
        color: var(--an-text);
        padding-bottom: 3rem;
    }

    /* ── Header ── */
    .an-header {
        background: var(--an-surface);
        border-bottom: 1px solid var(--an-border);
        padding: 2rem 2.5rem 1.6rem;
        position: relative;
        overflow: hidden;
    }
    .an-header::before {
        content: 'ANALISTA';
        position: absolute;
        right: 2rem; top: 50%;
        transform: translateY(-50%);
        font-family: 'IBM Plex Mono', monospace;
        font-size: 5rem;
        font-weight: 600;
        color: rgba(34,211,238,.04);
        letter-spacing: 8px;
        pointer-events: none;
        user-select: none;
    }
    .an-header-inner {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }
    .an-header-tag {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .65rem;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        color: var(--an-accent);
        margin-bottom: .5rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .an-header-tag::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--an-accent);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%,100% { opacity: 1; }
        50%      { opacity: .3; }
    }
    .an-header h1 {
        font-size: 1.7rem;
        font-weight: 700;
        margin: 0 0 .3rem;
        letter-spacing: -.4px;
        color: #f1f5f9;
    }
    .an-header p {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .78rem;
        color: var(--an-muted);
        margin: 0;
    }
    .an-user-chip {
        background: var(--an-accent-soft);
        border: 1px solid rgba(34,211,238,.2);
        border-radius: 6px;
        padding: .55rem 1rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .72rem;
        color: var(--an-accent);
        display: flex;
        align-items: center;
        gap: .5rem;
        white-space: nowrap;
    }

    /* ── Stat cards ── */
    .an-stats {
        background: var(--an-surface);
        border-bottom: 1px solid var(--an-border);
    }
    .an-stats-inner {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        flex-wrap: wrap;
    }
    .an-stat {
        flex: 1;
        min-width: 140px;
        padding: 1.1rem 1.6rem;
        border-right: 1px solid var(--an-border);
        position: relative;
    }
    .an-stat:last-child { border-right: none; }
    .an-stat-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .63rem;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--an-muted);
        margin-bottom: .4rem;
    }
    .an-stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1;
        font-family: 'IBM Plex Mono', monospace;
    }
    .an-stat-value.cyan   { color: var(--an-accent); }
    .an-stat-value.green  { color: var(--an-green); }
    .an-stat-value.amber  { color: var(--an-amber); }
    .an-stat-value.indigo { color: var(--an-indigo); }
    .an-stat-value.muted  { color: var(--an-muted); }
    .an-stat-sub {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .65rem;
        color: var(--an-muted);
        margin-top: .3rem;
    }

    /* ── Main ── */
    .an-main {
        max-width: 1280px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    /* ── Flash ── */
    .an-flash {
        border-radius: var(--radius);
        padding: .8rem 1.2rem;
        margin-bottom: 1.4rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .78rem;
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .an-flash.success { background: rgba(74,222,128,.1); color: var(--an-green); border: 1px solid rgba(74,222,128,.25); }
    .an-flash.error   { background: rgba(248,113,113,.1); color: var(--an-red);   border: 1px solid rgba(248,113,113,.25); }

    /* ── Filter panel ── */
    .an-filters {
        background: var(--an-card);
        border: 1px solid var(--an-border);
        border-radius: var(--radius);
        padding: 1.2rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .an-filters-title {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .65rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--an-accent);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .an-filters-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--an-border);
    }
    .an-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: .85rem;
        align-items: flex-end;
    }
    .an-filter-group {
        display: flex;
        flex-direction: column;
        gap: .3rem;
        flex: 1;
        min-width: 150px;
    }
    .an-filter-group label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .68rem;
        color: var(--an-muted);
        letter-spacing: .5px;
    }
    .an-filter-group select,
    .an-filter-group input {
        background: var(--an-surface);
        border: 1px solid var(--an-border);
        border-radius: 5px;
        padding: .5rem .75rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .8rem;
        color: var(--an-text);
        outline: none;
        transition: border-color .15s;
    }
    .an-filter-group select:focus,
    .an-filter-group input:focus { border-color: var(--an-accent); }
    .an-filter-group select option { background: var(--an-surface); }

    .an-btn {
        padding: .5rem 1.15rem;
        border-radius: 5px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .78rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        text-decoration: none;
        letter-spacing: .3px;
        white-space: nowrap;
    }
    .an-btn:hover { opacity: .85; transform: translateY(-1px); }
    .an-btn:active { transform: translateY(0); }
    .an-btn-primary { background: var(--an-accent); color: #0e1117; font-weight: 600; }
    .an-btn-outline { background: transparent; border: 1px solid var(--an-border); color: var(--an-muted); }
    .an-btn-export  { background: rgba(129,140,248,.15); border: 1px solid rgba(129,140,248,.3); color: var(--an-indigo); }

    /* ── Table card ── */
    .an-table-card {
        background: var(--an-card);
        border: 1px solid var(--an-border);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .an-table-head {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--an-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
        background: var(--an-surface);
    }
    .an-table-head h2 {
        font-size: .95rem;
        font-weight: 600;
        color: #f1f5f9;
        margin: 0;
        font-family: 'IBM Plex Sans', sans-serif;
    }
    .an-table-actions { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
    .an-count {
        background: var(--an-accent-soft);
        color: var(--an-accent);
        border-radius: 4px;
        padding: .18rem .65rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .68rem;
        font-weight: 600;
        border: 1px solid rgba(34,211,238,.2);
    }
    .an-readonly-badge {
        background: rgba(251,191,36,.08);
        color: var(--an-amber);
        border: 1px solid rgba(251,191,36,.2);
        border-radius: 4px;
        padding: .18rem .65rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .65rem;
        letter-spacing: .5px;
    }

    .an-table-wrap { overflow-x: auto; }
    table.an-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .83rem;
    }
    .an-table thead tr {
        background: var(--an-surface);
        border-bottom: 1px solid var(--an-border);
    }
    .an-table th {
        padding: .7rem 1rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .63rem;
        font-weight: 600;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--an-muted);
        text-align: left;
        white-space: nowrap;
    }
    .an-table td {
        padding: .85rem 1rem;
        border-bottom: 1px solid var(--an-border-soft);
        vertical-align: middle;
        color: var(--an-text);
    }
    .an-table tbody tr { transition: background .1s; }
    .an-table tbody tr:hover { background: rgba(34,211,238,.03); }
    .an-table tbody tr:last-child td { border-bottom: none; }

    /* ── Badges & tags ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        border-radius: 3px;
        padding: .2rem .6rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .67rem;
        font-weight: 600;
        letter-spacing: .4px;
        white-space: nowrap;
    }
    .badge-validado    { background: rgba(74,222,128,.1);   color: #4ade80; border: 1px solid rgba(74,222,128,.25); }
    .badge-en_atencion { background: rgba(96,165,250,.1);   color: #60a5fa; border: 1px solid rgba(96,165,250,.25); }
    .badge-resuelto    { background: rgba(148,163,184,.08); color: #94a3b8; border: 1px solid rgba(148,163,184,.2); }
    .badge-pendiente   { background: rgba(251,191,36,.1);   color: #fbbf24; border: 1px solid rgba(251,191,36,.25); }
    .badge-descartado  { background: rgba(248,113,113,.1);  color: #f87171; border: 1px solid rgba(248,113,113,.25); }

    .tipo-tag {
        background: var(--an-accent-soft);
        color: var(--an-accent);
        border-radius: 3px;
        padding: .18rem .55rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .67rem;
        border: 1px solid rgba(34,211,238,.15);
    }
    .id-mono {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .75rem;
        color: var(--an-indigo);
    }
    .mono-muted {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .72rem;
        color: var(--an-muted);
        white-space: nowrap;
    }
    .desc-cell {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: .8rem;
        color: #94a3b8;
    }

    /* ── Coords ── */
    .coords {
        font-family: 'IBM Plex Mono', monospace;
        font-size: .67rem;
        color: var(--an-muted);
        line-height: 1.5;
    }

    /* ── Pagination ── */
    .an-pagination {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--an-border);
        background: var(--an-surface);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: .72rem;
        color: var(--an-muted);
    }
    .an-pagination .links a,
    .an-pagination .links span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px; height: 28px;
        border-radius: 4px;
        font-size: .72rem;
        font-family: 'IBM Plex Mono', monospace;
        color: var(--an-muted);
        text-decoration: none;
        border: 1px solid var(--an-border);
        margin: 0 1px;
        transition: all .15s;
        background: var(--an-surface);
    }
    .an-pagination .links a:hover { border-color: var(--an-accent); color: var(--an-accent); }
    .an-pagination .links span.active { background: var(--an-accent); color: #0e1117; border-color: var(--an-accent); font-weight: 700; }

    /* ── Empty ── */
    .an-empty {
        text-align: center;
        padding: 4rem 1rem;
        color: var(--an-muted);
        font-family: 'IBM Plex Mono', monospace;
        font-size: .8rem;
    }
    .an-empty-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        opacity: .4;
    }

    @media (max-width: 768px) {
        .an-header { padding: 1.4rem 1.2rem 1.2rem; }
        .an-header h1 { font-size: 1.3rem; }
        .an-main { padding: 0 .75rem; }
        .an-header::before { display: none; }
    }
</style>

<div class="an-wrap">

    {{-- ── Header ── --}}
    <div class="an-header">
        <div class="an-header-inner">
            <div>
                <div class="an-header-tag">SafeRoad SC — Módulo de análisis</div>
                <h1>Panel Analista</h1>
                <p>Consulta de reportes aprobados · Solo lectura · Exportación CSV</p>
            </div>
            <div class="an-user-chip">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ auth()->user()->name ?? 'Analista' }}
            </div>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="an-stats">
        <div class="an-stats-inner">
            <div class="an-stat">
                <div class="an-stat-label">Total visible</div>
                <div class="an-stat-value cyan">{{ $stats['total'] }}</div>
                <div class="an-stat-sub">reportes aprobados</div>
            </div>
            <div class="an-stat">
                <div class="an-stat-label">Validados</div>
                <div class="an-stat-value indigo">{{ $stats['validado'] }}</div>
            </div>
            <div class="an-stat">
                <div class="an-stat-label">En atención</div>
                <div class="an-stat-value amber">{{ $stats['en_atencion'] }}</div>
            </div>
            <div class="an-stat">
                <div class="an-stat-label">Resueltos</div>
                <div class="an-stat-value green">{{ $stats['resuelto'] }}</div>
            </div>
            <div class="an-stat">
                <div class="an-stat-label">Municipios</div>
                <div class="an-stat-value muted">{{ $stats['municipios'] }}</div>
                <div class="an-stat-sub">con actividad</div>
            </div>
        </div>
    </div>

    {{-- ── Main ── --}}
    <div class="an-main">

        @if(session('success'))
            <div class="an-flash success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="an-flash error">✕ {{ session('error') }}</div>
        @endif

        {{-- ── Filtros ── --}}
        <form method="GET" action="{{ route('analista.index') }}">
            <div class="an-filters">
                <div class="an-filters-title">Filtros de análisis</div>
                <div class="an-filter-row">

                    <div class="an-filter-group">
                        <label>Municipio</label>
                        <select name="municipio">
                            <option value="">Todos</option>
                            @foreach($municipios as $m)
                                <option value="{{ $m }}" {{ request('municipio') === $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="an-filter-group">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            <option value="validado"    {{ request('estado') === 'validado'    ? 'selected' : '' }}>Validado</option>
                            <option value="en_atencion" {{ request('estado') === 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                            <option value="resuelto"    {{ request('estado') === 'resuelto'    ? 'selected' : '' }}>Resuelto</option>
                        </select>
                    </div>

                    <div class="an-filter-group">
                        <label>Tipo de riesgo</label>
                        <select name="tipo_riesgo">
                            <option value="">Todos</option>
                            @foreach($tipos as $t)
                                <option value="{{ $t }}" {{ request('tipo_riesgo') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="an-filter-group">
                        <label>Fecha desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    </div>

                    <div class="an-filter-group">
                        <label>Fecha hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    </div>

                    <div style="display:flex;gap:.5rem;align-self:flex-end">
                        <button type="submit" class="an-btn an-btn-primary">Filtrar</button>
                        <a href="{{ route('analista.index') }}" class="an-btn an-btn-outline">Limpiar</a>
                    </div>
                </div>
            </div>
        </form>

        {{-- ── Tabla ── --}}
        <div class="an-table-card">
            <div class="an-table-head">
                <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
                    <h2>Reportes aprobados</h2>
                    <span class="an-count">{{ $reportes->total() }} registros</span>
                    <span class="an-readonly-badge">SOLO LECTURA</span>
                </div>
                <div class="an-table-actions">
                    {{-- Exportar CSV con filtros actuales --}}
                    <a href="{{ route('analista.exportar') }}?{{ http_build_query(request()->only(['municipio','estado','tipo_riesgo','fecha_desde','fecha_hasta'])) }}"
                       class="an-btn an-btn-export">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Exportar CSV
                    </a>
                </div>
            </div>

            <div class="an-table-wrap">
                @if($reportes->isEmpty())
                    <div class="an-empty">
                        <div class="an-empty-icon">◎</div>
                        <p>No hay reportes que coincidan con los filtros aplicados.</p>
                    </div>
                @else
                <table class="an-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Municipio</th>
                            <th>Tipo de riesgo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Coordenadas</th>
                            <th>Registrado</th>
                            <th>Validado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $r)
                        <tr>
                            <td><span class="id-mono">#{{ $r->id }}</span></td>
                            <td class="mono-muted">{{ $r->municipio }}</td>
                            <td><span class="tipo-tag">{{ ucfirst(str_replace('_',' ',$r->tipo_riesgo)) }}</span></td>
                            <td class="desc-cell" title="{{ $r->descripcion }}">{{ $r->descripcion }}</td>
                            <td>
                                <span class="badge badge-{{ $r->estado }}">
                                    {{ ucfirst(str_replace('_',' ',$r->estado)) }}
                                </span>
                            </td>
                            <td>
                                <div class="coords">
                                    {{ number_format($r->latitud, 5) }}<br>
                                    {{ number_format($r->longitud, 5) }}
                                </div>
                            </td>
                            <td class="mono-muted">{{ $r->created_at->format('d/m/Y') }}</td>
                            <td class="mono-muted">
                                {{ $r->validado_at ? \Carbon\Carbon::parse($r->validado_at)->format('d/m/Y') : '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            @if($reportes->hasPages())
            <div class="an-pagination">
                <span>Mostrando {{ $reportes->firstItem() }}–{{ $reportes->lastItem() }} de {{ $reportes->total() }}</span>
                <div class="links">
                    {{ $reportes->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
