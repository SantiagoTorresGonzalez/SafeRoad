@extends('layouts.saferoad')

@section('title', 'SafeRoad SC — Mapa de siniestros viales')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        /* ── Layout principal ── */
        .mapa-container {
            display: flex;
            gap: 16px;
            height: calc(100vh - 98px);
        }

        #mapa {
            flex: 1;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            overflow: hidden;
            position: relative;
        }

        .panel-lateral {
            width: 310px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            padding-right: 2px;
        }

        /* ── Banner ── */
        .mapa-banner {
            background: linear-gradient(135deg, #0d6e4f 0%, #1e3a5f 100%);
            border-radius: 14px;
            padding: 16px 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-shrink: 0;
        }
        .mapa-banner h2 { font-size: 17px; font-weight: 700; margin: 0 0 2px; }
        .mapa-banner p  { font-size: 12px; opacity: .8; margin: 0; }

        .btn-reportar {
            background: rgba(255,255,255,.15);
            border: 1.5px solid rgba(255,255,255,.35);
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background .2s;
            font-family: 'Inter', sans-serif;
            white-space: nowrap;
        }
        .btn-reportar:hover { background: rgba(255,255,255,.25); }
        .btn-reportar.activo {
            background: white;
            color: #0d6e4f;
            border-color: white;
        }

        /* ── Tarjetas laterales ── */
        .sr-card {
            background: white;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #f1f5f9;
        }
        .sr-card-title {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 10px;
        }

        /* ── Leyenda ── */
        .leyenda-item {
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 12px;
            color: #475569;
            margin-bottom: 7px;
        }
        .leyenda-item:last-child { margin-bottom: 0; }
        .dot {
            width: 13px; height: 13px;
            border-radius: 50%; flex-shrink: 0;
            border: 2px solid rgba(0,0,0,0.12);
        }
        .dot-rojo    { background: #ef4444; }
        .dot-naranja { background: #f97316; }
        .dot-verde   { background: #22c55e; }
        .dot-gris    { background: #94a3b8; }

        /* ── Filtros ── */
        .filtros-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .filtro-select {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 12px;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: border-color .15s;
        }
        .filtro-select:focus { border-color: #0d6e4f; background: white; }

        .btn-limpiar {
            width: 100%;
            background: transparent;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 6px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background .15s;
        }
        .btn-limpiar:hover { background: #f1f5f9; }

        /* ── Stats ── */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .stat-item {
            background: #f8fafc;
            border-radius: 9px;
            padding: 10px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        .stat-label {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ── Barras municipios ── */
        .municipio-bar { margin-bottom: 7px; }
        .municipio-bar:last-child { margin-bottom: 0; }
        .municipio-bar-header {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #475569;
            margin-bottom: 3px;
        }
        .municipio-bar-header span:last-child { font-weight: 700; color: #0f172a; }
        .bar-track { background: #f1f5f9; border-radius: 4px; height: 4px; overflow: hidden; }
        .bar-fill  { background: linear-gradient(90deg, #0d6e4f, #1d4ed8); height: 100%; border-radius: 4px; }

        /* ── Formulario reporte ── */
        .form-reporte { display: none; }
        .form-reporte.visible { display: block; }

        .coords-display {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 11px;
            font-size: 11px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .coords-display.activo {
            color: #16a34a;
            border-color: #16a34a;
            background: #f0fdf4;
        }

        .form-group { margin-bottom: 10px; }
        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 4px;
        }
        .form-group select,
        .form-group textarea {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 12px;
            color: #0f172a;
            background: white;
            outline: none;
            font-family: 'Inter', sans-serif;
            transition: border-color .15s;
        }
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #0d6e4f;
            box-shadow: 0 0 0 3px rgba(13,110,79,.08);
        }
        .form-group textarea { resize: vertical; min-height: 70px; }

        .btn-enviar {
            width: 100%;
            background: linear-gradient(135deg, #0d6e4f, #15803d);
            color: white; border: none;
            padding: 10px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; margin-top: 4px;
            font-family: 'Inter', sans-serif;
            transition: opacity .2s;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .btn-enviar:hover { opacity: .9; }
        .btn-enviar:disabled { opacity: .6; cursor: not-allowed; }

        .btn-cancelar {
            width: 100%;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 8px; border-radius: 8px;
            font-size: 12px; font-weight: 500;
            cursor: pointer; margin-top: 6px;
            font-family: 'Inter', sans-serif;
        }

        /* ── Alertas ── */
        .alerta {
            padding: 9px 11px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 10px;
            display: none;
            line-height: 1.5;
        }
        .alerta-ok  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .alerta-err { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .alerta-warn{ background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

        .instruccion {
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.7;
            padding: 8px 0;
        }

        /* ── Contador de marcadores visibles ── */
        #contador-marcadores {
            position: absolute;
            top: 12px; left: 12px;
            background: white;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
            z-index: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Popup Leaflet personalizado ── */
        .leaflet-popup-content-wrapper {
            border-radius: 12px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,.15) !important;
            padding: 0 !important;
            overflow: hidden;
        }
        .leaflet-popup-content { margin: 0 !important; width: auto !important; min-width: 200px; }
        .leaflet-popup-tip-container { display: none; }

        .popup-header {
            padding: 10px 14px 8px;
            border-bottom: 1px solid #f1f5f9;
        }
        .popup-header h4 { font-size: 13px; font-weight: 700; color: #0f172a; margin: 0 0 4px; }
        .popup-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 10px; font-weight: 700; padding: 2px 8px;
            border-radius: 20px; text-transform: uppercase; letter-spacing: .04em;
        }
        .badge-rojo    { background: #fef2f2; color: #ef4444; }
        .badge-naranja { background: #fff7ed; color: #f97316; }
        .badge-verde   { background: #f0fdf4; color: #16a34a; }
        .badge-gris    { background: #f8fafc; color: #64748b; }

        .popup-body {
            padding: 10px 14px 12px;
            display: flex; flex-direction: column; gap: 5px;
        }
        .popup-row {
            display: flex; align-items: flex-start; gap: 6px;
            font-size: 12px; color: #475569;
        }
        .popup-row strong { color: #0f172a; min-width: 70px; flex-shrink: 0; }
    </style>
@endpush

@section('content')

    <div class="mapa-banner">
        <div>
            <h2>
                <span class="material-symbols-rounded" style="vertical-align:middle;font-size:19px">map</span>
                Mapa de siniestros viales
            </h2>
            <p>Provincia de Sabana Centro · Cundinamarca, Colombia</p>
        </div>
        <button class="btn-reportar" id="btn-reportar" onclick="activarReporte()">
            <span class="material-symbols-rounded" style="font-size:17px">add_location_alt</span>
            Reportar riesgo
        </button>
    </div>

    <div class="mapa-container">

        <div id="mapa">
            <div id="contador-marcadores">
                <span class="material-symbols-rounded" style="font-size:14px;color:#0d6e4f">location_on</span>
                <span id="num-marcadores">0</span> puntos visibles
            </div>
        </div>

        <div class="panel-lateral">

            {{-- Filtros --}}
            <div class="sr-card">
                <div class="sr-card-title">
                    <span class="material-symbols-rounded" style="font-size:13px;vertical-align:middle">filter_list</span>
                    Filtros
                </div>
                <div class="filtros-grid">
                    <select class="filtro-select" id="filtro-municipio" onchange="aplicarFiltros()">
                        <option value="">Todos los municipios</option>
                        <option>Cajicá</option>
                        <option>Chía</option>
                        <option>Cogua</option>
                        <option>Cota</option>
                        <option>Gachancipá</option>
                        <option>Nemocón</option>
                        <option>Sopó</option>
                        <option>Tabio</option>
                        <option>Tenjo</option>
                        <option>Tocancipá</option>
                        <option>Zipaquirá</option>
                    </select>
                    <select class="filtro-select" id="filtro-estado" onchange="aplicarFiltros()">
                        <option value="">Todos los estados</option>
                        <option value="validado">Validado (rojo)</option>
                        <option value="en_atencion">En atención (naranja)</option>
                        <option value="resuelto">Resuelto (verde)</option>
                        <option value="historico">Histórico INMLCF</option>
                    </select>
                    <select class="filtro-select" id="filtro-tipo" onchange="aplicarFiltros()">
                        <option value="">Todos los tipos</option>
                        <option value="cruce_sin_señalizacion">Cruce sin señalización</option>
                        <option value="falta_iluminacion">Falta de iluminación</option>
                        <option value="zona_alta_velocidad">Zona de alta velocidad</option>
                        <option value="superficie_deteriorada">Superficie deteriorada</option>
                        <option value="otro">Otro</option>
                    </select>
                    <button class="btn-limpiar" onclick="limpiarFiltros()">
                        ✕ Limpiar filtros
                    </button>
                </div>
            </div>

            {{-- Leyenda --}}
            <div class="sr-card">
                <div class="sr-card-title">Leyenda</div>
                <div class="leyenda-item">
                    <div class="dot dot-rojo"></div>
                    Validado por autoridad
                </div>
                <div class="leyenda-item">
                    <div class="dot dot-naranja"></div>
                    Intervención en proceso
                </div>
                <div class="leyenda-item">
                    <div class="dot dot-verde"></div>
                    Resuelto
                </div>
                <div class="leyenda-item">
                    <div class="dot dot-gris"></div>
                    Punto histórico INMLCF
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="sr-card">
                <div class="sr-card-title">Estadísticas</div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['total_reportes'] }}</div>
                        <div class="stat-label">Total reportes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color:#f97316">{{ $stats['reportes_hoy'] }}</div>
                        <div class="stat-label">Hoy</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color:#ef4444">{{ $stats['pendientes'] }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color:#1d4ed8">{{ $stats['en_atencion'] }}</div>
                        <div class="stat-label">En atención</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color:#16a34a">{{ $stats['resueltos'] }}</div>
                        <div class="stat-label">Resueltos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="font-size:12px;color:#0f172a">{{ $stats['municipio_top'] }}</div>
                        <div class="stat-label">Municipio top</div>
                    </div>
                </div>
            </div>

            {{-- Reportes por municipio --}}
            @if($stats['por_municipio']->count() > 0)
            <div class="sr-card">
                <div class="sr-card-title">Por municipio</div>
                @foreach($stats['por_municipio'] as $m)
                <div class="municipio-bar">
                    <div class="municipio-bar-header">
                        <span>{{ $m->municipio }}</span>
                        <span>{{ $m->total }}</span>
                    </div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:{{ $stats['total_reportes'] > 0 ? round(($m->total / $stats['total_reportes']) * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Formulario de reporte --}}
            <div class="sr-card" style="flex:1">
                <div class="instruccion" id="instruccion">
                    <span class="material-symbols-rounded" style="font-size:30px;color:#cbd5e1;display:block;margin-bottom:8px">add_location_alt</span>
                    Haz clic en <strong>Reportar riesgo</strong> y luego selecciona el punto en el mapa.
                </div>

                <div class="form-reporte" id="form-reporte">
                    <div class="sr-card-title">Nuevo reporte ciudadano</div>

                    <div class="alerta alerta-ok" id="alerta-ok">
                        ✅ ¡Reporte enviado! Quedará visible en el mapa una vez que la autoridad municipal lo valide.
                    </div>
                    <div class="alerta alerta-err" id="alerta-err">
                        ❌ No se pudo enviar. <span id="alerta-err-msg">Intenta de nuevo.</span>
                    </div>
                    <div class="alerta alerta-warn" id="alerta-warn">
                        ⏳ Reintentando envío... (<span id="intento-num">1</span>/3)
                    </div>

                    <div class="form-group">
                        <label>Ubicación seleccionada en el mapa</label>
                        <div class="coords-display" id="coords-display">
                            <span class="material-symbols-rounded" style="font-size:14px">my_location</span>
                            Haz clic en el mapa para seleccionar
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipo de riesgo *</label>
                        <select id="tipo_riesgo">
                            <option value="">Selecciona un tipo</option>
                            <option value="cruce_sin_señalizacion">Cruce sin señalización</option>
                            <option value="falta_iluminacion">Falta de iluminación</option>
                            <option value="zona_alta_velocidad">Zona de alta velocidad</option>
                            <option value="superficie_deteriorada">Superficie deteriorada</option>
                            <option value="atropello_peatonal">Riesgo de atropello peatonal</option>
                            <option value="cruce_escolar">Zona escolar sin protección</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Municipio *</label>
                        <select id="municipio">
                            <option value="">Selecciona el municipio</option>
                            <option>Cajicá</option>
                            <option>Chía</option>
                            <option>Cogua</option>
                            <option>Cota</option>
                            <option>Gachancipá</option>
                            <option>Nemocón</option>
                            <option>Sopó</option>
                            <option>Tabio</option>
                            <option>Tenjo</option>
                            <option>Tocancipá</option>
                            <option>Zipaquirá</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Descripción (opcional)</label>
                        <textarea id="descripcion" placeholder="Describe brevemente el riesgo que observaste..."></textarea>
                    </div>

                    <button class="btn-enviar" id="btn-enviar" onclick="enviarConRetry()">
                        <span class="material-symbols-rounded" style="font-size:16px">send</span>
                        Enviar reporte
                    </button>
                    <button class="btn-cancelar" onclick="cancelarReporte()">Cancelar</button>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ── Datos desde Laravel ──
    const reportes     = @json($reportes);
    const puntosRiesgo = @json($puntosRiesgo);

    // ── Estado ──
    let modoReporte  = false;
    let marcadorTemp = null;
    let latSelec     = null;
    let lngSelec     = null;
    let todosLosMarcadores = []; // [{marker, tipo, municipio, estado}]

    // ── Mapa ──
    const mapa = L.map('mapa', {
        center: [4.9833, -74.0167],
        zoom: 11,
        minZoom: 10,
        maxZoom: 17,
        maxBounds: [[4.72, -74.32], [5.20, -73.78]],
        maxBoundsViscosity: 0.9,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>'
    }).addTo(mapa);

    // ── Iconos ──
    function crearIcono(color, size = 14) {
        return L.divIcon({
            className: '',
            html: `<div style="
                width:${size}px; height:${size}px;
                background:${color};
                border-radius:50%;
                border:2.5px solid white;
                box-shadow:0 1px 5px rgba(0,0,0,0.35);
            "></div>`,
            iconSize: [size, size],
            iconAnchor: [size/2, size/2],
        });
    }

    const iconos = {
        validado:   crearIcono('#ef4444', 15),
        en_atencion:crearIcono('#f97316', 15),
        resuelto:   crearIcono('#22c55e', 15),
        historico:  crearIcono('#94a3b8', 13),
        temp:       crearIcono('#0d6e4f', 18),
    };

    // ── Popups ──
    function popupReporte(r) {
        const badgeClass = {
            validado: 'badge-rojo',
            en_atencion: 'badge-naranja',
            resuelto: 'badge-verde',
        }[r.estado] || 'badge-gris';

        const estadoLabel = {
            validado: '● Validado',
            en_atencion: '● En atención',
            resuelto: '● Resuelto',
        }[r.estado] || r.estado;

        const tipo = r.tipo_riesgo.replace(/_/g, ' ');

        return `
            <div class="popup-header">
                <h4>📍 Reporte ciudadano</h4>
                <span class="popup-badge ${badgeClass}">${estadoLabel}</span>
            </div>
            <div class="popup-body">
                <div class="popup-row"><strong>Municipio:</strong> ${r.municipio}</div>
                <div class="popup-row"><strong>Tipo:</strong> ${tipo}</div>
                ${r.descripcion ? `<div class="popup-row"><strong>Detalle:</strong> ${r.descripcion}</div>` : ''}
            </div>
        `;
    }

    function popupHistorico(p) {
        return `
            <div class="popup-header">
                <h4>⚠️ Zona histórica INMLCF</h4>
                <span class="popup-badge badge-gris">● Histórico ${p.anio ?? ''}</span>
            </div>
            <div class="popup-body">
                <div class="popup-row"><strong>Municipio:</strong> ${p.municipio}</div>
                ${p.total_muertes ? `<div class="popup-row"><strong>Muertes:</strong> ${p.total_muertes}</div>` : ''}
                ${p.descripcion   ? `<div class="popup-row"><strong>Detalle:</strong> ${p.descripcion}</div>` : ''}
            </div>
        `;
    }

    // ── Pintar marcadores ──
    puntosRiesgo.forEach(p => {
        const m = L.marker([p.latitud, p.longitud], { icon: iconos.historico })
            .addTo(mapa)
            .bindPopup(popupHistorico(p), { maxWidth: 240 });
        todosLosMarcadores.push({ marker: m, tipo: '', municipio: p.municipio, estado: 'historico' });
    });

    reportes.forEach(r => {
        const icono = iconos[r.estado] || iconos.validado;
        const m = L.marker([r.latitud, r.longitud], { icon: icono })
            .addTo(mapa)
            .bindPopup(popupReporte(r), { maxWidth: 240 });
        todosLosMarcadores.push({ marker: m, tipo: r.tipo_riesgo, municipio: r.municipio, estado: r.estado });
    });

    actualizarContador();

    // ── Filtros ──
    function aplicarFiltros() {
        const filtMun    = document.getElementById('filtro-municipio').value;
        const filtEstado = document.getElementById('filtro-estado').value;
        const filtTipo   = document.getElementById('filtro-tipo').value;
        let visibles = 0;

        todosLosMarcadores.forEach(item => {
            let mostrar = true;
            if (filtMun    && item.municipio !== filtMun)   mostrar = false;
            if (filtEstado && item.estado    !== filtEstado) mostrar = false;
            if (filtTipo   && item.tipo      !== filtTipo)   mostrar = false;

            if (mostrar) {
                if (!mapa.hasLayer(item.marker)) mapa.addLayer(item.marker);
                visibles++;
            } else {
                if (mapa.hasLayer(item.marker)) mapa.removeLayer(item.marker);
            }
        });

        document.getElementById('num-marcadores').textContent = visibles;
    }

    function limpiarFiltros() {
        document.getElementById('filtro-municipio').value = '';
        document.getElementById('filtro-estado').value    = '';
        document.getElementById('filtro-tipo').value      = '';
        aplicarFiltros();
    }

    function actualizarContador() {
        document.getElementById('num-marcadores').textContent = todosLosMarcadores.length;
    }

    // ── Modo reporte ──
    mapa.on('click', function(e) {
        if (!modoReporte) return;
        latSelec = e.latlng.lat.toFixed(7);
        lngSelec = e.latlng.lng.toFixed(7);
        if (marcadorTemp) mapa.removeLayer(marcadorTemp);
        marcadorTemp = L.marker([latSelec, lngSelec], { icon: iconos.temp }).addTo(mapa);
        const el = document.getElementById('coords-display');
        el.innerHTML = `<span class="material-symbols-rounded" style="font-size:14px">check_circle</span> Lat: ${latSelec} | Lng: ${lngSelec}`;
        el.classList.add('activo');
    });

    function activarReporte() {
        modoReporte = true;
        document.getElementById('instruccion').style.display = 'none';
        document.getElementById('form-reporte').classList.add('visible');
        document.getElementById('mapa').style.cursor = 'crosshair';
        const btn = document.getElementById('btn-reportar');
        btn.classList.add('activo');
        btn.innerHTML = '<span class="material-symbols-rounded" style="font-size:17px">my_location</span> Haz clic en el mapa';
    }

    function cancelarReporte() {
        modoReporte = false;
        latSelec = null; lngSelec = null;
        if (marcadorTemp) { mapa.removeLayer(marcadorTemp); marcadorTemp = null; }
        document.getElementById('form-reporte').classList.remove('visible');
        document.getElementById('instruccion').style.display = 'block';
        document.getElementById('coords-display').innerHTML = '<span class="material-symbols-rounded" style="font-size:14px">my_location</span> Haz clic en el mapa para seleccionar';
        document.getElementById('coords-display').classList.remove('activo');
        document.getElementById('alerta-ok').style.display   = 'none';
        document.getElementById('alerta-err').style.display  = 'none';
        document.getElementById('alerta-warn').style.display = 'none';
        document.getElementById('mapa').style.cursor = '';
        const btn = document.getElementById('btn-reportar');
        btn.classList.remove('activo');
        btn.innerHTML = '<span class="material-symbols-rounded" style="font-size:17px">add_location_alt</span> Reportar riesgo';
    }

    // ── Envío con retry automático (3 intentos) ──
    async function enviarConRetry() {
        const tipo      = document.getElementById('tipo_riesgo').value;
        const municipio = document.getElementById('municipio').value;
        const desc      = document.getElementById('descripcion').value;

        if (!latSelec || !lngSelec) {
            alert('Primero haz clic en el mapa para seleccionar la ubicación.');
            return;
        }
        if (!tipo)      { alert('Selecciona el tipo de riesgo.'); return; }
        if (!municipio) { alert('Selecciona el municipio.'); return; }

        const btn = document.getElementById('btn-enviar');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-rounded" style="font-size:16px;animation:spin .8s linear infinite">autorenew</span> Enviando...';

        document.getElementById('alerta-ok').style.display   = 'none';
        document.getElementById('alerta-err').style.display  = 'none';
        document.getElementById('alerta-warn').style.display = 'none';

        const MAX_INTENTOS = 3;
        let exito = false;

        for (let intento = 1; intento <= MAX_INTENTOS; intento++) {
            if (intento > 1) {
                document.getElementById('alerta-warn').style.display = 'block';
                document.getElementById('intento-num').textContent   = intento;
                await new Promise(r => setTimeout(r, 1200 * intento)); // espera creciente
            }

            try {
                const datos = new FormData();
                datos.append('_token',      document.querySelector('meta[name="csrf-token"]').content);
                datos.append('tipo_riesgo', tipo);
                datos.append('latitud',     latSelec);
                datos.append('longitud',    lngSelec);
                datos.append('municipio',   municipio);
                datos.append('descripcion', desc);

                const res = await fetch('{{ route("mapa.store") }}', {
                    method: 'POST',
                    body: datos,
                    signal: AbortSignal.timeout(8000), // timeout 8s por intento
                });

                if (res.ok) {
                    exito = true;
                    break;
                }
            } catch (e) {
                // timeout o error de red → reintento
            }
        }

        document.getElementById('alerta-warn').style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-rounded" style="font-size:16px">send</span> Enviar reporte';

        if (exito) {
            document.getElementById('alerta-ok').style.display = 'block';
            document.getElementById('tipo_riesgo').value  = '';
            document.getElementById('municipio').value    = '';
            document.getElementById('descripcion').value  = '';
            if (marcadorTemp) { mapa.removeLayer(marcadorTemp); marcadorTemp = null; }
            latSelec = null; lngSelec = null;
            document.getElementById('coords-display').innerHTML = '<span class="material-symbols-rounded" style="font-size:14px">my_location</span> Haz clic en el mapa para seleccionar';
            document.getElementById('coords-display').classList.remove('activo');
            modoReporte = false;
            document.getElementById('mapa').style.cursor = '';
            const btn2 = document.getElementById('btn-reportar');
            btn2.classList.remove('activo');
            btn2.innerHTML = '<span class="material-symbols-rounded" style="font-size:17px">add_location_alt</span> Reportar riesgo';
        } else {
            document.getElementById('alerta-err').style.display = 'block';
            document.getElementById('alerta-err-msg').textContent = 'Fallaron los 3 intentos. Verifica tu conexión.';
        }
    }

    // ── CSS spin para botón de carga ──
    const styleEl = document.createElement('style');
    styleEl.textContent = `@keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }`;
    document.head.appendChild(styleEl);
</script>
@endpush
