@extends('layouts.saferoad')

@section('title', 'SafeRoad SC — Mapa de siniestros viales')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        .mapa-container {
            display: flex;
            gap: 20px;
            height: calc(100vh - 96px);
        }

        #mapa {
            flex: 1;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .panel-lateral {
            width: 320px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow-y: auto;
        }

        .leyenda-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #475569;
            margin-bottom: 8px;
        }

        .leyenda-item:last-child { margin-bottom: 0; }

        .dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            flex-shrink: 0;
            border: 2px solid rgba(0,0,0,0.1);
        }

        .dot-rojo    { background: #ef4444; }
        .dot-naranja { background: #f97316; }
        .dot-verde   { background: #22c55e; }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat-item {
            background: #f8fafc;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .stat-number {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }

        .stat-label {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .form-reporte { display: none; }
        .form-reporte.visible { display: block; }

        .coords-display {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12px;
            color: #94a3b8;
        }

        .coords-display.activo {
            color: #16a34a;
            border-color: #16a34a;
            background: #f0fdf4;
        }

        .form-group { margin-bottom: 12px; }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 4px;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            color: #0f172a;
            background: white;
            outline: none;
            font-family: 'Inter', sans-serif;
        }

        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #116dff;
            box-shadow: 0 0 0 3px rgba(17,109,255,0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-reportar {
            background: #116dff;
            color: white;
            border: none;
            padding: 9px 18px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-reportar:hover { background: #0058d6; }

        .btn-enviar {
            width: 100%;
            background: #116dff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 4px;
            font-family: 'Inter', sans-serif;
            transition: background 0.2s;
        }

        .btn-enviar:hover { background: #0058d6; }

        .btn-cancelar {
            width: 100%;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 9px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 6px;
            font-family: 'Inter', sans-serif;
        }

        .alerta {
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 12px;
            display: none;
        }

        .alerta-ok {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .alerta-err {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .instruccion {
            font-size: 13px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.6;
            padding: 8px 0;
        }

        .sr-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #f1f5f9;
        }

        .sr-card-title {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .mapa-banner {
            background: linear-gradient(135deg, #116dff 0%, #0058d6 100%);
            border-radius: 12px;
            padding: 20px 24px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .mapa-banner h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .mapa-banner p {
            font-size: 13px;
            opacity: 0.85;
            margin: 0;
        }
    </style>
@endpush

@section('content')

    <div class="mapa-banner">
        <div>
            <h2>
                <span class="material-symbols-rounded" style="vertical-align: middle; font-size: 22px;">map</span>
                Mapa de siniestros viales
            </h2>
            <p>Provincia de Sabana Centro, Cundinamarca · Datos INMLCF 2024</p>
        </div>
        <button class="btn-reportar" onclick="activarReporte()">
            <span class="material-symbols-rounded" style="font-size: 18px;">add_location_alt</span>
            Reportar punto de riesgo
        </button>
    </div>

    <div class="mapa-container">

        <div id="mapa"></div>

        <div class="panel-lateral">

            <div class="sr-card">
                <div class="sr-card-title">Leyenda</div>
                <div class="leyenda-item">
                    <div class="dot dot-rojo"></div>
                    Alta accidentalidad histórica
                </div>
                <div class="leyenda-item">
                    <div class="dot dot-naranja"></div>
                    Reporte ciudadano pendiente
                </div>
                <div class="leyenda-item">
                    <div class="dot dot-verde"></div>
                    Punto atendido por autoridad
                </div>
            </div>

            <div class="sr-card">
                <div class="sr-card-title">Estadísticas de la plataforma</div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['total_reportes'] }}</div>
                        <div class="stat-label">Total reportes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: #f97316;">{{ $stats['reportes_hoy'] }}</div>
                        <div class="stat-label">Reportes hoy</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: #ef4444;">{{ $stats['pendientes'] }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: #116dff;">{{ $stats['en_atencion'] }}</div>
                        <div class="stat-label">En atención</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: #22c55e;">{{ $stats['resueltos'] }}</div>
                        <div class="stat-label">Resueltos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="font-size: 14px; color: #0f172a;">{{ $stats['municipio_top'] }}</div>
                        <div class="stat-label">Municipio con más reportes</div>
                    </div>
                </div>
            </div>

            {{-- Reportes por municipio --}}
            @if($stats['por_municipio']->count() > 0)
            <div class="sr-card">
                <div class="sr-card-title">Reportes por municipio</div>
                @foreach($stats['por_municipio'] as $m)
                    <div style="margin-bottom: 8px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #475569; margin-bottom: 3px;">
                            <span>{{ $m->municipio }}</span>
                            <span style="font-weight: 600; color: #0f172a;">{{ $m->total }}</span>
                        </div>
                        <div style="background: #f1f5f9; border-radius: 4px; height: 5px; overflow: hidden;">
                            <div style="
                                background: #116dff;
                                height: 100%;
                                width: {{ $stats['total_reportes'] > 0 ? ($m->total / $stats['total_reportes']) * 100 : 0 }}%;
                                border-radius: 4px;
                            "></div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <div class="sr-card" style="flex: 1;">

                <div class="instruccion" id="instruccion">
                    <span class="material-symbols-rounded" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;">add_location_alt</span>
                    Haz clic en <strong>Reportar punto de riesgo</strong> y selecciona la ubicación en el mapa.
                </div>

                <div class="form-reporte" id="form-reporte">
                    <div class="sr-card-title">Nuevo reporte</div>

                    <div class="alerta alerta-ok" id="alerta-ok">
                        ✅ Reporte enviado. Quedará visible una vez validado.
                    </div>
                    <div class="alerta alerta-err" id="alerta-err">
                        ❌ Error al enviar. Intenta de nuevo.
                    </div>

                    <div class="form-group">
                        <label>Ubicación seleccionada</label>
                        <div class="coords-display" id="coords-display">
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
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Municipio *</label>
                        <select id="municipio">
                            <option value="">Selecciona el municipio</option>
                            <option value="Cajicá">Cajicá</option>
                            <option value="Chía">Chía</option>
                            <option value="Cogua">Cogua</option>
                            <option value="Cota">Cota</option>
                            <option value="Gachancipá">Gachancipá</option>
                            <option value="Nemocón">Nemocón</option>
                            <option value="Sopó">Sopó</option>
                            <option value="Tabio">Tabio</option>
                            <option value="Tenjo">Tenjo</option>
                            <option value="Tocancipá">Tocancipá</option>
                            <option value="Zipaquirá">Zipaquirá</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Descripción (opcional)</label>
                        <textarea id="descripcion" placeholder="Describe brevemente el punto de riesgo..."></textarea>
                    </div>

                    <button class="btn-enviar" onclick="enviarReporte()">
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
    const reportes     = @json($reportes);
    const puntosRiesgo = @json($puntosRiesgo);

    let modoReporte  = false;
    let marcadorTemp = null;
    let latSelec     = null;
    let lngSelec     = null;

    const mapa = L.map('mapa', {
        center: [4.9833, -74.0167],
        zoom: 11,
        minZoom: 10,
        maxZoom: 17,
        maxBounds: [[4.82, -74.22], [5.15, -73.88]],
        maxBoundsViscosity: 1.0,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa);

    function crearIcono(color) {
        return L.divIcon({
            className: '',
            html: `<div style="
                width:14px; height:14px;
                background:${color};
                border-radius:50%;
                border:2px solid white;
                box-shadow:0 1px 4px rgba(0,0,0,0.3);
            "></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });
    }

    const iconoRojo    = crearIcono('#ef4444');
    const iconoNaranja = crearIcono('#f97316');
    const iconoVerde   = crearIcono('#22c55e');

    puntosRiesgo.forEach(p => {
        L.marker([p.latitud, p.longitud], { icon: iconoRojo })
            .addTo(mapa)
            .bindPopup(`
                <b>⚠️ Zona de alto riesgo</b><br>
                <b>Municipio:</b> ${p.municipio}<br>
                <b>Muertes ${p.anio}:</b> ${p.total_muertes}<br>
                ${p.descripcion ? `<b>Descripción:</b> ${p.descripcion}` : ''}
            `);
    });

    reportes.forEach(r => {
        const icono = r.estado === 'resuelto' ? iconoVerde : iconoNaranja;
        L.marker([r.latitud, r.longitud], { icon: icono })
            .addTo(mapa)
            .bindPopup(`
                <b>📍 Reporte ciudadano</b><br>
                <b>Tipo:</b> ${r.tipo_riesgo.replace(/_/g,' ')}<br>
                <b>Municipio:</b> ${r.municipio}<br>
                <b>Estado:</b> ${r.estado.replace(/_/g,' ')}<br>
                ${r.descripcion ? `<b>Descripción:</b> ${r.descripcion}` : ''}
            `);
    });

    mapa.on('click', function(e) {
        if (!modoReporte) return;
        latSelec = e.latlng.lat.toFixed(7);
        lngSelec = e.latlng.lng.toFixed(7);
        if (marcadorTemp) mapa.removeLayer(marcadorTemp);
        marcadorTemp = L.marker([latSelec, lngSelec], {
            icon: L.divIcon({
                className: '',
                html: `<div style="
                    width:18px; height:18px;
                    background:#116dff;
                    border-radius:50%;
                    border:3px solid white;
                    box-shadow:0 0 8px rgba(17,109,255,0.6);
                "></div>`,
                iconSize: [18, 18],
                iconAnchor: [9, 9],
            })
        }).addTo(mapa);
        const coordsEl = document.getElementById('coords-display');
        coordsEl.textContent = `Lat: ${latSelec} | Lng: ${lngSelec}`;
        coordsEl.classList.add('activo');
    });

    function activarReporte() {
        modoReporte = true;
        document.getElementById('instruccion').style.display = 'none';
        document.getElementById('form-reporte').classList.add('visible');
        document.getElementById('mapa').style.cursor = 'crosshair';
    }

    function cancelarReporte() {
        modoReporte = false;
        latSelec = null; lngSelec = null;
        if (marcadorTemp) { mapa.removeLayer(marcadorTemp); marcadorTemp = null; }
        document.getElementById('form-reporte').classList.remove('visible');
        document.getElementById('instruccion').style.display = 'block';
        document.getElementById('coords-display').textContent = 'Haz clic en el mapa para seleccionar';
        document.getElementById('coords-display').classList.remove('activo');
        document.getElementById('alerta-ok').style.display  = 'none';
        document.getElementById('alerta-err').style.display = 'none';
        document.getElementById('mapa').style.cursor = '';
    }

    async function enviarReporte() {
        const tipo      = document.getElementById('tipo_riesgo').value;
        const municipio = document.getElementById('municipio').value;
        const desc      = document.getElementById('descripcion').value;

        if (!latSelec || !lngSelec) { alert('Selecciona una ubicación en el mapa primero.'); return; }
        if (!tipo)      { alert('Selecciona el tipo de riesgo.'); return; }
        if (!municipio) { alert('Selecciona el municipio.'); return; }

        const datos = new FormData();
        datos.append('_token',      document.querySelector('meta[name="csrf-token"]').content);
        datos.append('tipo_riesgo', tipo);
        datos.append('latitud',     latSelec);
        datos.append('longitud',    lngSelec);
        datos.append('municipio',   municipio);
        datos.append('descripcion', desc);

        try {
            const res = await fetch('{{ route("mapa.store") }}', { method: 'POST', body: datos });
            if (res.ok) {
                document.getElementById('alerta-ok').style.display  = 'block';
                document.getElementById('alerta-err').style.display = 'none';
                document.getElementById('tipo_riesgo').value = '';
                document.getElementById('municipio').value   = '';
                document.getElementById('descripcion').value = '';
                if (marcadorTemp) { mapa.removeLayer(marcadorTemp); marcadorTemp = null; }
                latSelec = null; lngSelec = null;
                document.getElementById('coords-display').textContent = 'Haz clic en el mapa para seleccionar';
                document.getElementById('coords-display').classList.remove('activo');
                modoReporte = false;
                document.getElementById('mapa').style.cursor = '';
            } else {
                document.getElementById('alerta-err').style.display = 'block';
            }
        } catch (e) {
            document.getElementById('alerta-err').style.display = 'block';
        }
    }
</script>
@endpush