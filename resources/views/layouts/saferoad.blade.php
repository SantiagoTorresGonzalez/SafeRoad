<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SafeRoad SC')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <link rel="stylesheet" href="{{ asset('css/layouts/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/cards.css') }}">

    @stack('styles')

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar SafeRoad */
        .sr-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .sr-navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #0f172a;
        }

        .sr-navbar-icon {
            width: 32px;
            height: 32px;
            background: #116dff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .sr-navbar-brand h1 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }

        .sr-navbar-brand span {
            font-size: 12px;
            color: #94a3b8;
            display: block;
            font-weight: 400;
            line-height: 1;
        }

        .sr-navbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sr-nav-link {
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sr-nav-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .sr-nav-link.active {
            background: #eff6ff;
            color: #116dff;
        }

        .sr-btn-login {
            background: #116dff;
            color: white;
            border: none;
            padding: 7px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .sr-btn-login:hover { background: #0058d6; }

        /* Contenido principal */
        .sr-main {
            flex: 1;
            padding: 20px 24px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="sr-navbar">
        <a href="{{ url('/mapa') }}" class="sr-navbar-brand">
            <div class="sr-navbar-icon">
                <span class="material-symbols-rounded" style="font-size: 18px;">shield</span>
            </div>
            <div>
                <h1>SafeRoad SC</h1>
                <span>Siniestros viales · Sabana Centro</span>
            </div>
        </a>

        <div class="sr-navbar-actions">
            <a href="{{ url('/mapa') }}" class="sr-nav-link {{ request()->is('mapa') ? 'active' : '' }}">
                <span class="material-symbols-rounded" style="font-size: 18px;">map</span>
                Mapa
            </a>

            @auth
                <a href="{{ url('/dashboard') }}" class="sr-nav-link">
                    <span class="material-symbols-rounded" style="font-size: 18px;">dashboard</span>
                    Panel
                </a>
                <a href="{{ url('/panel-autoridad') }}" class="sr-nav-link {{ request()->is('panel-autoridad*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded" style="font-size: 18px;">admin_panel_settings</span>
                    Gestión
                </a>
            @else
                <a href="{{ route('login') }}" class="sr-btn-login">
                    <span class="material-symbols-rounded" style="font-size: 16px;">login</span>
                    Acceso institucional
                </a>
            @endauth
        </div>
    </nav>

    <!-- Contenido -->
    <main class="sr-main">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>