<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SafeRoad SC — Seguridad vial en Sabana Centro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --verde:      #0d6e4f;
            --verde-med:  #15803d;
            --verde-claro:#dcfce7;
            --azul:       #1e3a5f;
            --azul-med:   #1d4ed8;
            --azul-claro: #dbeafe;
            --accent:     #f59e0b;
            --text-dark:  #0f172a;
            --text-mid:   #475569;
            --text-light: #94a3b8;
            --bg:         #f8fafc;
            --white:      #ffffff;
            --shadow-sm:  0 1px 3px rgba(0,0,0,.08);
            --shadow-md:  0 4px 16px rgba(0,0,0,.10);
            --shadow-lg:  0 12px 40px rgba(0,0,0,.14);
            --radius:     14px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── NAV ── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 900;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px;
            box-shadow: var(--shadow-sm);
        }

        .nav-brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; color: var(--text-dark);
        }
        .nav-brand-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--verde), var(--azul));
            display: flex; align-items: center; justify-content: center;
            color: white;
        }
        .nav-brand h1 { font-size: 15px; font-weight: 700; }
        .nav-brand span { font-size: 11px; color: var(--text-light); display: block; }

        .nav-actions { display: flex; align-items: center; gap: 10px; }

        .btn-nav-map {
            background: linear-gradient(135deg, var(--verde), var(--verde-med));
            color: white; border: none; padding: 8px 20px; border-radius: 30px;
            font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
            display: flex; align-items: center; gap: 6px;
            transition: opacity .2s, transform .2s; font-family: 'Inter', sans-serif;
            box-shadow: 0 2px 8px rgba(13,110,79,.3);
        }
        .btn-nav-map:hover { opacity: .9; transform: translateY(-1px); }

        .btn-nav-login {
            background: var(--azul); color: white; border: none;
            padding: 8px 18px; border-radius: 30px; font-size: 13px;
            font-weight: 600; cursor: pointer; text-decoration: none;
            display: flex; align-items: center; gap: 6px;
            transition: opacity .2s, transform .2s; font-family: 'Inter', sans-serif;
            box-shadow: 0 2px 8px rgba(30,58,95,.3);
        }
        .btn-nav-login:hover { opacity: .9; transform: translateY(-1px); }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(150deg, #0a2540 0%, #1e3a5f 45%, #0d6e4f 100%);
            display: flex; align-items: center; justify-content: center;
            text-align: center; padding: 80px 24px 60px;
            position: relative; overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
            color: white; font-size: 12px; font-weight: 600; padding: 6px 14px;
            border-radius: 30px; margin-bottom: 24px; letter-spacing: .5px;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(36px, 6vw, 70px);
            font-weight: 900; color: white; line-height: 1.08;
            margin-bottom: 20px; letter-spacing: -1.5px;
        }
        .hero h1 em { font-style: normal; color: #6ee7b7; }

        .hero p {
            font-size: clamp(15px, 2.5vw, 19px); color: rgba(255,255,255,.75);
            max-width: 640px; margin: 0 auto 40px; line-height: 1.7;
        }

        .hero-cta { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

        .btn-hero-map {
            background: linear-gradient(135deg, #16a34a, #0d6e4f);
            color: white; padding: 15px 32px; border-radius: 50px;
            font-size: 15px; font-weight: 700; text-decoration: none;
            display: flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 20px rgba(22,163,74,.4);
            transition: transform .2s, box-shadow .2s;
        }
        .btn-hero-map:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(22,163,74,.5); }

        .btn-hero-login {
            background: rgba(255,255,255,.1); color: white;
            border: 2px solid rgba(255,255,255,.3); padding: 14px 28px;
            border-radius: 50px; font-size: 15px; font-weight: 600;
            text-decoration: none; display: flex; align-items: center; gap: 8px;
            backdrop-filter: blur(8px); transition: background .2s, transform .2s;
        }
        .btn-hero-login:hover { background: rgba(255,255,255,.2); transform: translateY(-3px); }

        .hero-stats {
            display: flex; gap: 40px; justify-content: center; flex-wrap: wrap;
            margin-top: 60px; padding-top: 40px;
            border-top: 1px solid rgba(255,255,255,.12);
        }
        .hero-stat { text-align: center; }
        .hero-stat strong { display: block; font-size: 32px; font-weight: 800; color: white; }
        .hero-stat span { font-size: 13px; color: rgba(255,255,255,.6); }

        /* ── SECTION ── */
        section { padding: 80px 24px; }

        .container { max-width: 1140px; margin: 0 auto; }

        .section-label {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--verde-claro); color: var(--verde);
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .8px; padding: 5px 12px; border-radius: 20px;
            margin-bottom: 14px;
        }
        .section-title { font-size: clamp(26px, 4vw, 40px); font-weight: 800; color: var(--text-dark); margin-bottom: 16px; }
        .section-sub { font-size: 16px; color: var(--text-mid); max-width: 560px; line-height: 1.7; }

        /* ── HOW IT WORKS ── */
        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px; margin-top: 48px;
        }

        .step {
            background: white; border-radius: var(--radius); padding: 28px 24px;
            border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm);
            transition: transform .2s, box-shadow .2s;
        }
        .step:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        .step-num {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, var(--verde), var(--azul-med));
            color: white; font-size: 18px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .step h3 { font-size: 15px; font-weight: 700; margin-bottom: 8px; }
        .step p { font-size: 13px; color: var(--text-mid); line-height: 1.6; }

        /* ── ROLES ── */
        .roles-bg { background: white; }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px; margin-top: 48px;
        }

        .role-card {
            border-radius: var(--radius); padding: 28px 24px;
            border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm);
            transition: transform .2s, box-shadow .2s;
        }
        .role-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        .role-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 16px; color: white;
        }
        .role-icon.ciudadano    { background: linear-gradient(135deg, #0d6e4f, #16a34a); }
        .role-icon.autoridad    { background: linear-gradient(135deg, #1e3a5f, #2563eb); }
        .role-icon.planificador { background: linear-gradient(135deg, #7c3aed, #a855f7); }
        .role-icon.analista     { background: linear-gradient(135deg, #b45309, #f59e0b); }

        .role-card h3 { font-size: 15px; font-weight: 700; margin-bottom: 8px; }
        .role-card p { font-size: 13px; color: var(--text-mid); line-height: 1.6; }

        /* ── STATES ── */
        .states-bg { background: linear-gradient(135deg, #0a2540 0%, #0d6e4f 100%); }
        .states-bg .section-title { color: white; }
        .states-bg .section-sub { color: rgba(255,255,255,.7); }
        .states-bg .section-label { background: rgba(255,255,255,.12); color: #6ee7b7; }

        .states-grid {
            display: flex; flex-wrap: wrap; gap: 14px; margin-top: 40px;
        }

        .state-chip {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);
            border-radius: 50px; padding: 10px 18px; color: white;
        }
        .state-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
        .state-chip h4 { font-size: 13px; font-weight: 600; }
        .state-chip p { font-size: 11px; color: rgba(255,255,255,.6); }

        /* ── MUNICIPIOS ── */
        .municipios-grid {
            display: flex; flex-wrap: wrap; gap: 10px; margin-top: 32px;
        }
        .municipio-tag {
            background: white; border: 1px solid #e2e8f0;
            border-radius: 8px; padding: 8px 14px;
            font-size: 13px; font-weight: 500; color: var(--text-mid);
            display: flex; align-items: center; gap: 6px;
        }
        .municipio-tag span { font-size: 16px; }

        /* ── CTA FINAL ── */
        .cta-bg {
            background: linear-gradient(135deg, #0d6e4f 0%, #1e3a5f 100%);
            text-align: center; padding: 80px 24px;
        }
        .cta-bg h2 { font-size: clamp(28px, 4vw, 44px); font-weight: 800; color: white; margin-bottom: 16px; }
        .cta-bg p { font-size: 17px; color: rgba(255,255,255,.75); margin-bottom: 36px; }

        /* ── FOOTER ── */
        footer {
            background: #0a2540; color: rgba(255,255,255,.5);
            text-align: center; padding: 24px;
            font-size: 13px;
        }
        footer strong { color: rgba(255,255,255,.8); }

        /* ── CHATBOT FLOTANTE ── */
        #chat-fab {
            position: fixed; bottom: 28px; right: 28px; z-index: 1000;
            width: 60px; height: 60px; border-radius: 50%;
            background: linear-gradient(135deg, #0d6e4f, #1e3a5f);
            box-shadow: 0 4px 20px rgba(13,110,79,.45);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; border: none; color: white;
            transition: transform .2s, box-shadow .2s;
            animation: pulse-fab 2.5s infinite;
        }
        #chat-fab:hover { transform: scale(1.1); box-shadow: 0 8px 30px rgba(13,110,79,.55); animation: none; }

        @keyframes pulse-fab {
            0%, 100% { box-shadow: 0 4px 20px rgba(13,110,79,.45); }
            50% { box-shadow: 0 4px 28px rgba(13,110,79,.7), 0 0 0 8px rgba(13,110,79,.1); }
        }

        #chat-fab .chat-badge {
            position: absolute; top: -2px; right: -2px;
            width: 18px; height: 18px; border-radius: 50%;
            background: #ef4444; color: white; font-size: 10px;
            font-weight: 700; display: flex; align-items: center; justify-content: center;
            border: 2px solid white;
        }

        #chat-panel {
            position: fixed; bottom: 104px; right: 28px; z-index: 999;
            width: 340px; max-height: 500px;
            background: white; border-radius: 18px;
            box-shadow: 0 12px 48px rgba(0,0,0,.18);
            display: none; flex-direction: column; overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        #chat-panel.visible { display: flex; }

        .chat-header {
            background: linear-gradient(135deg, #0d6e4f, #1e3a5f);
            padding: 16px 18px;
            display: flex; align-items: center; gap: 12px;
        }
        .chat-header-icon {
            width: 38px; height: 38px; border-radius: 50%;
            background: rgba(255,255,255,.2); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; color: white;
        }
        .chat-header-info h4 { font-size: 14px; font-weight: 700; color: white; }
        .chat-header-info p { font-size: 11px; color: rgba(255,255,255,.7); }
        .chat-close { margin-left: auto; background: none; border: none; color: rgba(255,255,255,.8); cursor: pointer; }

        .chat-premium-banner {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            padding: 8px 14px; display: flex; align-items: center; gap: 8px;
            font-size: 11px; color: white; font-weight: 600;
        }
        .chat-premium-banner button {
            margin-left: auto; background: white; color: #d97706;
            border: none; border-radius: 20px; padding: 3px 10px;
            font-size: 10px; font-weight: 700; cursor: pointer; white-space: nowrap;
        }

        .chat-messages {
            flex: 1; overflow-y: auto; padding: 16px;
            display: flex; flex-direction: column; gap: 10px;
        }

        .msg { max-width: 80%; font-size: 13px; line-height: 1.5; }
        .msg-bot { align-self: flex-start; }
        .msg-user { align-self: flex-end; }

        .msg-bubble {
            padding: 10px 14px; border-radius: 16px;
        }
        .msg-bot .msg-bubble {
            background: #f1f5f9; color: var(--text-dark);
            border-bottom-left-radius: 4px;
        }
        .msg-user .msg-bubble {
            background: linear-gradient(135deg, #0d6e4f, #16a34a); color: white;
            border-bottom-right-radius: 4px;
        }

        .chat-quick-btns {
            display: flex; flex-wrap: wrap; gap: 6px; padding: 0 16px 10px;
        }
        .quick-btn {
            background: var(--verde-claro); color: var(--verde);
            border: none; border-radius: 20px; padding: 5px 12px;
            font-size: 11px; font-weight: 600; cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background .15s;
        }
        .quick-btn:hover { background: #bbf7d0; }

        .chat-input-row {
            border-top: 1px solid #e2e8f0;
            padding: 10px 14px; display: flex; gap: 8px; align-items: center;
        }
        .chat-input {
            flex: 1; border: 1px solid #e2e8f0; border-radius: 20px;
            padding: 7px 14px; font-size: 13px; font-family: 'Inter', sans-serif;
            outline: none; color: var(--text-dark);
        }
        .chat-input:focus { border-color: var(--verde); }
        .chat-send {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--verde); color: white; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background .2s;
        }
        .chat-send:hover { background: var(--verde-med); }

        /* ── AUTH GUARD en chat ── */
        .chat-auth-notice {
            padding: 24px; text-align: center;
        }
        .chat-auth-notice span { font-size: 36px; display: block; margin-bottom: 12px; }
        .chat-auth-notice h4 { font-size: 15px; font-weight: 700; margin-bottom: 8px; }
        .chat-auth-notice p { font-size: 13px; color: var(--text-mid); margin-bottom: 16px; }
        .btn-auth-register {
            background: var(--verde); color: white; text-decoration: none;
            padding: 10px 20px; border-radius: 30px; font-size: 13px; font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="nav">
    <a href="{{ url('/') }}" class="nav-brand">
        <div class="nav-brand-icon">
            <span class="material-symbols-rounded" style="font-size:19px">shield</span>
        </div>
        <div>
            <h1>SafeRoad SC</h1>
            <span>Sabana Centro, Cundinamarca</span>
        </div>
    </a>
    <div class="nav-actions">
        <a href="{{ route('mapa.index') }}" class="btn-nav-map">
            <span class="material-symbols-rounded" style="font-size:17px">map</span>
            Ver Mapa
        </a>

        @auth
            {{-- Usuario autenticado: mostrar acceso al panel y botón salir --}}
            @php
                $rol = auth()->user()->role?->name ?? 'ciudadano';
                $panelRuta = match($rol) {
                    'autoridad_municipal'      => route('panel.index'),
                    'planificador_territorial' => route('planificador.index'),
                    'analista'                 => route('analista.index'),
                    default                    => null,
                };
            @endphp

            @if($panelRuta)
                <a href="{{ $panelRuta }}" class="btn-nav-login">
                    <span class="material-symbols-rounded" style="font-size:17px">dashboard</span>
                    Mi Panel
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="btn-nav-login" style="border:none;cursor:pointer;font-family:inherit">
                    <span class="material-symbols-rounded" style="font-size:17px">logout</span>
                    Salir
                </button>
            </form>
        @else
            {{-- Sin sesión: mostrar Iniciar Sesión --}}
            <a href="{{ route('login') }}" class="btn-nav-login">
                <span class="material-symbols-rounded" style="font-size:17px">login</span>
                Iniciar Sesión
            </a>
        @endauth
    </div>
</nav>

<!-- ── HERO ── -->
<section class="hero">
    <div>
        <div class="hero-badge">
            <span class="material-symbols-rounded" style="font-size:14px">location_on</span>
            11 municipios · Cundinamarca, Colombia
        </div>
        <h1>Reporta un siniestro.<br><em>Salva una vida.</em></h1>
        <p>
            SafeRoad SC es la plataforma colaborativa de seguridad vial para Sabana Centro.
            Ciudadanos, autoridades y planificadores trabajando juntos para hacer nuestras vías más seguras.
        </p>
        <div class="hero-cta">
            <a href="{{ route('mapa.index') }}" class="btn-hero-map">
                <span class="material-symbols-rounded">map</span>
                Ver Mapa Interactivo
            </a>
            <a href="{{ route('login') }}" class="btn-hero-login">
                <span class="material-symbols-rounded">admin_panel_settings</span>
                Acceso Institucional
            </a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <strong>11</strong>
                <span>Municipios cubiertos</span>
            </div>
            <div class="hero-stat">
                <strong>24/7</strong>
                <span>Reporte disponible</span>
            </div>
        </div>
    </div>
</section>

<!-- ── CÓMO FUNCIONA ── -->
<section>
    <div class="container">
        <div class="section-label">
            <span class="material-symbols-rounded" style="font-size:14px">route</span>
            Cómo funciona
        </div>
        <h2 class="section-title">Tan fácil como 4 pasos</h2>
        <p class="section-sub">Sin registrarte, puedes ver el mapa y enviar reportes. Es así de simple.</p>

        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Abre el mapa</h3>
                <p>Haz clic en "Ver Mapa" y accede al mapa interactivo de Sabana Centro con todos los puntos de riesgo confirmados.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Elige la ubicación</h3>
                <p>Haz clic directamente en el punto del mapa donde ocurrió o existe el siniestro vial o riesgo.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Describe el problema</h3>
                <p>Selecciona el tipo de riesgo, añade una descripción y opcionalmente una foto. No necesitas cuenta.</p>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <h3>La autoridad actúa</h3>
                <p>La autoridad municipal revisa tu reporte, lo valida y activa la intervención. El punto aparece en el mapa.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── ESTADOS ── -->
<section class="states-bg">
    <div class="container">
        <div class="section-label">
            <span class="material-symbols-rounded" style="font-size:14px">timeline</span>
            Ciclo de vida
        </div>
        <h2 class="section-title">Cada reporte tiene un camino</h2>
        <p class="section-sub">Desde que el ciudadano reporta hasta que el problema se resuelve, todo queda registrado y visible.</p>

        <div class="states-grid">
            <div class="state-chip">
                <div class="state-dot" style="background:#94a3b8"></div>
                <div>
                    <h4>Pendiente</h4>
                    <p>Enviado, sin revisar</p>
                </div>
            </div>
            <div class="state-chip">
                <div class="state-dot" style="background:#ef4444"></div>
                <div>
                    <h4>Validado</h4>
                    <p>Confirmado · punto rojo en mapa</p>
                </div>
            </div>
            <div class="state-chip">
                <div class="state-dot" style="background:#f97316"></div>
                <div>
                    <h4>En atención</h4>
                    <p>Intervención iniciada · punto naranja</p>
                </div>
            </div>
            <div class="state-chip">
                <div class="state-dot" style="background:#22c55e"></div>
                <div>
                    <h4>Resuelto</h4>
                    <p>Problema solucionado · punto verde</p>
                </div>
            </div>
            <div class="state-chip">
                <div class="state-dot" style="background:#6366f1"></div>
                <div>
                    <h4>Descartado</h4>
                    <p>No válido · sin punto en mapa</p>
                </div>
            </div>
            <div class="state-chip">
                <div class="state-dot" style="background:#d1d5db"></div>
                <div>
                    <h4>Cerrado automático</h4>
                    <p>3 meses después de resuelto</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── MUNICIPIOS ── -->
<section>
    <div class="container">
        <div class="section-label">
            <span class="material-symbols-rounded" style="font-size:14px">location_city</span>
            Cobertura
        </div>
        <h2 class="section-title">11 municipios de Sabana Centro</h2>
        <p class="section-sub">Todos los municipios de la provincia están cubiertos por la plataforma.</p>

        <div class="municipios-grid">
            @foreach(['Zipaquirá', 'Chía', 'Cajicá', 'Sopó', 'Tocancipá', 'Gachancipá', 'Tabio', 'Tenjo', 'Cogua', 'Nemocón', 'Suesca'] as $m)
            <div class="municipio-tag">
                <span>📍</span> {{ $m }}
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ── CTA FINAL ── -->
<section class="cta-bg">
    <div class="container">
        <h2>¿Ves un riesgo en tu vía?</h2>
        <p>Repórtalo ahora. Sin cuenta. Sin formularios. En 30 segundos.</p>
        <a href="{{ route('mapa.index') }}" class="btn-hero-map" style="display:inline-flex">
            <span class="material-symbols-rounded">map</span>
            Abrir el Mapa Ahora
        </a>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer>
    <p><strong>SafeRoad SC</strong> · Plataforma colaborativa de seguridad vial · Sabana Centro, Cundinamarca, Colombia</p>
    <p style="margin-top:6px">Desarrollado para la gestión ciudadana e institucional de siniestros viales · 2026</p>
</footer>

<!-- ── CHATBOT FLOTANTE ── -->
<button id="chat-fab" onclick="toggleChat()" title="Abrir asistente SafeRoad">
    <span class="material-symbols-rounded" style="font-size:26px">chat</span>
    <div class="chat-badge">1</div>
</button>

<div id="chat-panel">
    <div class="chat-header">
        <div class="chat-header-icon">
            <span class="material-symbols-rounded" style="font-size:20px">shield</span>
        </div>
        <div class="chat-header-info">
            <h4>Asistente SafeRoad</h4>
            <p>En línea · responde al instante</p>
        </div>
        <button class="chat-close" onclick="toggleChat()">
            <span class="material-symbols-rounded" style="font-size:20px">close</span>
        </button>
    </div>

    {{-- Si el usuario NO está autenticado --}}
    @guest
    <div class="chat-auth-notice">
        <span>🔒</span>
        <h4>Inicia sesión para usar el asistente</h4>
        <p>El chatbot de SafeRoad SC está disponible para usuarios registrados. ¡El registro es gratuito!</p>
        <a href="{{ route('register') }}" class="btn-auth-register">Regístrate gratis</a>
        <p style="margin-top:10px; font-size:12px; color:#94a3b8">¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color:#0d6e4f">Inicia sesión</a></p>
    </div>
    @endguest

    {{-- Si el usuario SÍ está autenticado --}}
    @auth
    <div class="chat-premium-banner">
        <span class="material-symbols-rounded" style="font-size:15px">auto_awesome</span>
        ¿Interesado en la IA predictiva? ¡Hazte Premium!
        <button onclick="startPremiumFlow()">Saber más</button>
    </div>

    <div class="chat-messages" id="chat-messages">
        <div class="msg msg-bot">
            <div class="msg-bubble">
                ¡Hola, <strong>{{ Auth::user()->name }}</strong>! 👋 Soy el asistente de SafeRoad SC. ¿En qué te ayudo hoy?
            </div>
        </div>
    </div>

    <div class="chat-quick-btns" id="quick-btns">
        <button class="quick-btn" onclick="sendQuick('¿Qué es SafeRoad SC?')">¿Qué es SafeRoad SC?</button>
        <button class="quick-btn" onclick="sendQuick('¿Cómo reporto un siniestro?')">¿Cómo reporto?</button>
        <button class="quick-btn" onclick="sendQuick('¿Qué significan los colores del mapa?')">Colores del mapa</button>
        <button class="quick-btn" onclick="sendQuick('¿Cómo veo el estado de mi reporte?')">Estado de reporte</button>
    </div>

    <div class="chat-input-row">
        <input class="chat-input" id="chat-input" type="text" placeholder="Escribe tu pregunta..." onkeydown="if(event.key==='Enter') sendMessage()">
        <button class="chat-send" onclick="sendMessage()">
            <span class="material-symbols-rounded" style="font-size:18px">send</span>
        </button>
    </div>
    @endauth
</div>

<script>
    // ── Toggle chatbot ──
    function toggleChat() {
        const panel = document.getElementById('chat-panel');
        const fab   = document.getElementById('chat-fab');
        panel.classList.toggle('visible');
        const badge = fab.querySelector('.chat-badge');
        if (badge) badge.remove();
    }

    // ── Respuestas predeterminadas ──
    const faq = {
        '¿qué es saferoad sc?':
            'SafeRoad SC es una plataforma colaborativa de gestión de siniestros viales para los 11 municipios de Sabana Centro, Cundinamarca. Permite a ciudadanos reportar riesgos y a autoridades gestionarlos.',
        '¿cómo reporto un siniestro?':
            'Es muy fácil: abre el mapa, haz clic en el punto donde está el riesgo, selecciona el tipo, añade una descripción y envía. ¡No necesitas cuenta!',
        '¿qué significan los colores del mapa?':
            '🔴 Rojo = riesgo validado por autoridad\n🟠 Naranja = intervención en proceso\n🟢 Verde = problema resuelto',
        '¿cómo veo el estado de mi reporte?':
            'Por el momento puedes ver el estado de un reporte observando su color en el mapa. Próximamente habrá notificaciones por correo.',
        '¿cómo me registro?':
            'Haz clic en "Iniciar Sesión" y luego en "Regístrate aquí". El registro es gratuito y solo requiere nombre, correo y contraseña.',
        '¿qué es la ia predictiva?':
            'La IA predictiva es una funcionalidad futura que analizará patrones de siniestros para anticipar zonas de riesgo. Está disponible en el plan Premium. ¡Pulsa el botón amarillo para más info!',
    };

    function botReply(text) {
        const msgs = document.getElementById('chat-messages');
        const div  = document.createElement('div');
        div.className = 'msg msg-bot';
        div.innerHTML = `<div class="msg-bubble">${text.replace(/\n/g,'<br>')}</div>`;
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function userMsg(text) {
        const msgs = document.getElementById('chat-messages');
        const div  = document.createElement('div');
        div.className = 'msg msg-user';
        div.innerHTML = `<div class="msg-bubble">${text}</div>`;
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function sendQuick(q) {
        userMsg(q);
        const key = q.toLowerCase().trim();
        let reply = faq[key] || Object.entries(faq).find(([k]) => key.includes(k.split(' ')[1] ?? ''))?.[1];
        setTimeout(() => botReply(reply || 'No tengo una respuesta específica para eso, pero puedes explorar el mapa o escribirnos. 😊'), 400);
    }

    function sendMessage() {
        const input = document.getElementById('chat-input');
        const text  = input.value.trim();
        if (!text) return;
        input.value = '';
        userMsg(text);
        const key = text.toLowerCase();
        const match = Object.entries(faq).find(([k]) => key.includes(k.replace(/[¿?]/g,'').trim().split(' ').slice(0,3).join(' ')));
        const reply = match ? match[1] : '¡Buena pregunta! Por ahora respondo preguntas frecuentes sobre SafeRoad SC. Prueba con los botones de acceso rápido. 😊';
        setTimeout(() => botReply(reply), 400);
    }

   // ── Flujo Premium ──
    function startPremiumFlow() {
        botReply('¡Genial! ¿Quieres que te enviemos toda la información del plan Premium a tu correo registrado?');
        const qb = document.getElementById('quick-btns');
        if (qb) {
            qb.innerHTML = `
                <button class="quick-btn" onclick="sendPremiumEmail()">📧 Sí, enviar a mi correo</button>
                <button class="quick-btn" onclick="botReply('¡Sin problema! Puedes consultarnos cuando quieras. 😊')">No por ahora</button>
            `;
        }
    }

    function sendPremiumEmail() {
        const qb = document.getElementById('quick-btns');
        if (qb) qb.innerHTML = '';
        userMsg('📧 Sí, enviar a mi correo');

        fetch('{{ route("chatbot.premium") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            botReply(data.message || '✅ ¡Correo enviado! Revisa tu bandeja de entrada.');
        })
        .catch(() => {
            botReply('⚠ No pudimos enviar el correo en este momento. Intenta de nuevo.');
        });

        setTimeout(() => {
            if (qb) qb.innerHTML = `<button class="quick-btn" onclick="location.reload()">Volver al inicio</button>`;
        }, 1500);
    }
</script>

</body>
</html>
