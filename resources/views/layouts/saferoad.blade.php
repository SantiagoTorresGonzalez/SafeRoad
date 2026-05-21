<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SafeRoad SC')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <link rel="stylesheet" href="{{ asset('css/layouts/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/cards.css') }}">

    @stack('styles')

    <style>
        :root {
            --sr-verde:      #0d6e4f;
            --sr-verde-med:  #15803d;
            --sr-verde-claro:#dcfce7;
            --sr-azul:       #1e3a5f;
            --sr-azul-med:   #1d4ed8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0; padding: 0;
            min-height: 100vh;
            display: flex; flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Navbar ── */
        .sr-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px; height: 58px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 800;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
        }

        .sr-navbar-brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; color: #0f172a;
        }
        .sr-navbar-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: linear-gradient(135deg, var(--sr-verde), var(--sr-azul));
            display: flex; align-items: center; justify-content: center; color: white;
        }
        .sr-navbar-brand h1 { font-size: 15px; font-weight: 700; margin: 0; color: #0f172a; }
        .sr-navbar-brand span { font-size: 11px; color: #94a3b8; display: block; font-weight: 400; line-height: 1; }

        .sr-navbar-actions { display: flex; align-items: center; gap: 10px; }

        .sr-nav-link {
            font-size: 13px; color: #64748b; text-decoration: none;
            padding: 6px 12px; border-radius: 8px; font-weight: 500;
            transition: background .15s, color .15s;
            display: flex; align-items: center; gap: 6px;
        }
        .sr-nav-link:hover { background: #f1f5f9; color: #0f172a; }
        .sr-nav-link.active { background: #f0fdf4; color: var(--sr-verde); }

        .sr-btn-login {
            background: linear-gradient(135deg, var(--sr-verde), var(--sr-azul));
            color: white; border: none; padding: 7px 16px;
            border-radius: 30px; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            display: flex; align-items: center; gap: 6px;
            transition: opacity .2s; font-family: 'Inter', sans-serif;
        }
        .sr-btn-login:hover { opacity: .9; }

        .sr-main { flex: 1; padding: 20px 24px; }

        /* ── Chatbot flotante ── */
        #chat-fab {
            position: fixed; bottom: 26px; right: 26px; z-index: 900;
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, var(--sr-verde), var(--sr-azul));
            box-shadow: 0 4px 18px rgba(13,110,79,.4);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; border: none; color: white;
            transition: transform .2s, box-shadow .2s;
            animation: pulse-sr 2.5s infinite;
        }
        #chat-fab:hover { transform: scale(1.1); animation: none; box-shadow: 0 6px 28px rgba(13,110,79,.55); }

        @keyframes pulse-sr {
            0%,100% { box-shadow: 0 4px 18px rgba(13,110,79,.4); }
            50%      { box-shadow: 0 4px 24px rgba(13,110,79,.65), 0 0 0 7px rgba(13,110,79,.08); }
        }

        #chat-panel {
            position: fixed; bottom: 96px; right: 26px; z-index: 899;
            width: 330px; max-height: 480px;
            background: white; border-radius: 18px;
            box-shadow: 0 12px 48px rgba(0,0,0,.16);
            display: none; flex-direction: column; overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        #chat-panel.visible { display: flex; }

        .chat-header {
            background: linear-gradient(135deg, var(--sr-verde), var(--sr-azul));
            padding: 14px 16px; display: flex; align-items: center; gap: 10px;
        }
        .chat-hicon {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,.2); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; color: white;
        }
        .chat-hinfo h4 { font-size: 13px; font-weight: 700; color: white; }
        .chat-hinfo p  { font-size: 11px; color: rgba(255,255,255,.7); }
        .chat-close { margin-left: auto; background: none; border: none; color: rgba(255,255,255,.8); cursor: pointer; }

        .chat-premium-bar {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            padding: 7px 12px; display: flex; align-items: center; gap: 7px;
            font-size: 11px; color: white; font-weight: 600;
        }
        .chat-premium-bar button {
            margin-left: auto; background: white; color: #d97706;
            border: none; border-radius: 20px; padding: 3px 9px;
            font-size: 10px; font-weight: 700; cursor: pointer; white-space: nowrap;
        }

        .chat-msgs {
            flex: 1; overflow-y: auto; padding: 14px;
            display: flex; flex-direction: column; gap: 9px;
        }
        .msg { max-width: 82%; font-size: 13px; line-height: 1.5; }
        .msg-bot  { align-self: flex-start; }
        .msg-user { align-self: flex-end; }
        .msg-bubble { padding: 9px 13px; border-radius: 14px; }
        .msg-bot  .msg-bubble { background: #f1f5f9; color: #0f172a; border-bottom-left-radius: 4px; }
        .msg-user .msg-bubble { background: linear-gradient(135deg, var(--sr-verde), var(--sr-verde-med)); color: white; border-bottom-right-radius: 4px; }

        .chat-quick {
            display: flex; flex-wrap: wrap; gap: 5px; padding: 0 14px 9px;
        }
        .qbtn {
            background: var(--sr-verde-claro); color: var(--sr-verde);
            border: none; border-radius: 20px; padding: 4px 11px;
            font-size: 11px; font-weight: 600; cursor: pointer;
            font-family: 'Inter', sans-serif; transition: background .15s;
        }
        .qbtn:hover { background: #bbf7d0; }

        .chat-input-row {
            border-top: 1px solid #e2e8f0;
            padding: 9px 12px; display: flex; gap: 7px; align-items: center;
        }
        .chat-inp {
            flex: 1; border: 1px solid #e2e8f0; border-radius: 20px;
            padding: 7px 13px; font-size: 13px; font-family: 'Inter', sans-serif;
            outline: none; color: #0f172a;
        }
        .chat-inp:focus { border-color: var(--sr-verde); }
        .chat-send-btn {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--sr-verde); color: white; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .chat-send-btn:hover { background: var(--sr-verde-med); }

        .chat-auth-notice { padding: 22px; text-align: center; }
        .chat-auth-notice .icon { font-size: 34px; display: block; margin-bottom: 10px; }
        .chat-auth-notice h4 { font-size: 14px; font-weight: 700; margin-bottom: 7px; }
        .chat-auth-notice p  { font-size: 12px; color: #64748b; margin-bottom: 14px; }
        .chat-auth-btn {
            background: var(--sr-verde); color: white; text-decoration: none;
            padding: 9px 18px; border-radius: 28px; font-size: 12px; font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="sr-navbar">
        <a href="{{ url('/') }}" class="sr-navbar-brand">
            <div class="sr-navbar-icon">
                <span class="material-symbols-rounded" style="font-size:17px">shield</span>
            </div>
            <div>
                <h1>SafeRoad SC</h1>
                <span>Siniestros viales · Sabana Centro</span>
            </div>
        </a>

        <div class="sr-navbar-actions">
            <a href="{{ url('/mapa') }}" class="sr-nav-link {{ request()->is('mapa') ? 'active' : '' }}">
                <span class="material-symbols-rounded" style="font-size:17px">map</span>
                Mapa
            </a>

            @auth
                <a href="{{ url('/panel-autoridad') }}" class="sr-nav-link {{ request()->is('panel-autoridad*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded" style="font-size:17px">admin_panel_settings</span>
                    Panel
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="sr-btn-login" style="background:transparent;color:#64748b;box-shadow:none;border:1px solid #e2e8f0;">
                        <span class="material-symbols-rounded" style="font-size:15px">logout</span>
                        Salir
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="sr-btn-login">
                    <span class="material-symbols-rounded" style="font-size:15px">login</span>
                    Acceso institucional
                </a>
            @endauth
        </div>
    </nav>

    <!-- Contenido -->
    <main class="sr-main">
        @yield('content')
    </main>

    <!-- Chatbot flotante -->
    <button id="chat-fab" onclick="srToggleChat()" title="Asistente SafeRoad">
        <span class="material-symbols-rounded" style="font-size:24px">chat</span>
    </button>

    <div id="chat-panel">
        <div class="chat-header">
            <div class="chat-hicon">
                <span class="material-symbols-rounded" style="font-size:18px">shield</span>
            </div>
            <div class="chat-hinfo">
                <h4>Asistente SafeRoad</h4>
                <p>En línea · responde al instante</p>
            </div>
            <button class="chat-close" onclick="srToggleChat()">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>

        @guest
        <div class="chat-auth-notice">
            <span class="icon">🔒</span>
            <h4>Inicia sesión para chatear</h4>
            <p>El asistente está disponible para usuarios registrados. Es gratis.</p>
            <a href="{{ route('register') }}" class="chat-auth-btn">Regístrate gratis</a>
            <p style="margin-top:9px;font-size:11px;color:#94a3b8">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color:var(--sr-verde)">Inicia sesión</a>
            </p>
        </div>
        @endguest

        @auth
        <div class="chat-premium-bar">
            <span class="material-symbols-rounded" style="font-size:14px">auto_awesome</span>
            ¿Interesado en la IA predictiva? ¡Hazte Premium!
            <button onclick="srPremiumFlow()">Saber más</button>
        </div>

        <div class="chat-msgs" id="sr-chat-msgs">
            <div class="msg msg-bot">
                <div class="msg-bubble">
                    ¡Hola, <strong>{{ Auth::user()->name }}</strong>! 👋 Soy tu asistente de SafeRoad SC.
                </div>
            </div>
        </div>

        <div class="chat-quick" id="sr-quick">
            <button class="qbtn" onclick="srQuick('¿Qué es SafeRoad SC?')">¿Qué es?</button>
            <button class="qbtn" onclick="srQuick('¿Cómo reporto?')">¿Cómo reporto?</button>
            <button class="qbtn" onclick="srQuick('Colores del mapa')">Colores del mapa</button>
            <button class="qbtn" onclick="srQuick('Estado de mi reporte')">Estado de reporte</button>
        </div>

        <div class="chat-input-row">
            <input class="chat-inp" id="sr-inp" type="text" placeholder="Escribe tu pregunta..." onkeydown="if(event.key==='Enter') srSend()">
            <button class="chat-send-btn" id="sr-send-btn" onclick="srSend()">
                <span class="material-symbols-rounded" style="font-size:16px">send</span>
            </button>
        </div>
        @endauth
    </div>

    <script>
    function srToggleChat() {
        document.getElementById('chat-panel').classList.toggle('visible');
    }

    const srFaq = {
        'qué es saferoad sc':     'SafeRoad SC es la plataforma colaborativa de gestión de siniestros viales para los 11 municipios de Sabana Centro. Ciudadanos reportan riesgos, las autoridades los validan y los planificadores gestionan las intervenciones.',
        'cómo reporto':           'Abre el mapa, haz clic en el punto de riesgo, elige el tipo de siniestro, añade descripción y envía. ¡Sin cuenta!',
        'colores del mapa':       '🔴 Rojo = validado por autoridad\n🟠 Naranja = intervención en curso\n🟢 Verde = resuelto',
        'estado de mi reporte':   'Puedes ver el estado de tu reporte por el color del punto en el mapa. Próximamente habrá notificaciones por correo.',
        'ia predictiva':          'La IA predictiva analizará patrones para anticipar zonas de riesgo. Es una funcionalidad futura del plan Premium.',
    };

    function srBotMsg(text) {
        const c = document.getElementById('sr-chat-msgs');
        const d = document.createElement('div');
        d.className = 'msg msg-bot';
        d.innerHTML = `<div class="msg-bubble">${text.replace(/\n/g,'<br>')}</div>`;
        c.appendChild(d); c.scrollTop = c.scrollHeight;
    }
    function srUserMsg(text) {
        const c = document.getElementById('sr-chat-msgs');
        const d = document.createElement('div');
        d.className = 'msg msg-user';
        d.innerHTML = `<div class="msg-bubble">${text}</div>`;
        c.appendChild(d); c.scrollTop = c.scrollHeight;
    }

    function srQuick(q) {
        srUserMsg(q);
        const key = q.toLowerCase().replace(/[¿?]/g,'').trim();
        const match = Object.entries(srFaq).find(([k]) => key.includes(k.split(' ')[0]) || k.includes(key.split(' ')[0]));
        setTimeout(() => srBotMsg(match ? match[1] : 'No tengo esa respuesta aún, pero puedes explorar el mapa o preguntar algo diferente. 😊'), 350);
    }

    function srSend() {
        const inp = document.getElementById('sr-inp');
        const txt = inp.value.trim(); if (!txt) return; inp.value = '';
        srUserMsg(txt);
        const key = txt.toLowerCase().replace(/[¿?]/g,'');
        const match = Object.entries(srFaq).find(([k]) => key.includes(k.split(' ')[0]));
        setTimeout(() => srBotMsg(match ? match[1] : 'Buena pregunta. Por ahora respondo FAQs de SafeRoad SC. Prueba con los botones rápidos. 😊'), 350);
    }

    let _srPremCh = '';
    function srPremiumFlow() {
        srBotMsg('¡Genial! La IA predictiva está en desarrollo. ¿Prefieres que te contactemos por <strong>WhatsApp</strong> o <strong>correo electrónico</strong>?');
        document.getElementById('sr-quick').innerHTML = `
            <button class="qbtn" onclick="srPremCh('whatsapp')">📱 WhatsApp</button>
            <button class="qbtn" onclick="srPremCh('email')">📧 Correo electrónico</button>
        `;
    }
    function srPremCh(ch) {
        _srPremCh = ch;
        srUserMsg(ch === 'whatsapp' ? '📱 WhatsApp' : '📧 Correo electrónico');
        const label = ch === 'whatsapp' ? 'número de WhatsApp (con indicativo, ej: +57 300...)' : 'dirección de correo electrónico';
        setTimeout(() => srBotMsg(`Perfecto. Escríbeme tu ${label} y te avisamos pronto.`), 350);
        document.getElementById('sr-quick').innerHTML = '';
        const inp = document.getElementById('sr-inp');
        inp.placeholder = ch === 'whatsapp' ? '+57 300 000 0000' : 'tu@correo.com';
        document.getElementById('sr-send-btn').onclick = srPremSend;
        inp.onkeydown = (e) => { if (e.key === 'Enter') srPremSend(); };
    }
    function srPremSend() {
        const inp = document.getElementById('sr-inp');
        const val = inp.value.trim(); if (!val) return; inp.value = '';
        srUserMsg(val);
        fetch('{{ route("chatbot.premium") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ canal: _srPremCh, contacto: val })
        }).catch(() => {});
        setTimeout(() => {
            srBotMsg('✅ ¡Recibido! Pronto te contactaremos sobre SafeRoad SC Premium. ¡Gracias! 🚀');
            document.getElementById('sr-quick').innerHTML = '';
        }, 350);
    }
    </script>

    @stack('scripts')
</body>
</html>
