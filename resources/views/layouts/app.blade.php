{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dewey Accounts')</title>
    
    <!-- Fonts - SF Pro Display (Apple) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols (Google Icons) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
        <!-- Centralized CSS -->
        <link rel="stylesheet" href="{{ asset('css/layouts/theme.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layouts/navbar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layouts/sidebar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/cards.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/tables.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/modals.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    
        @stack('styles')
    @auth
        {{-- Role specific styles removed to maintain consistent design --}}
    @endauth
        <style>
            /* Sidebar Dropdown Styles */
            details.sidebar-dropdown summary {
                list-style: none;
                cursor: pointer;
            }
            details.sidebar-dropdown summary::-webkit-details-marker {
                display: none;
            }
            details.sidebar-dropdown[open] summary .expand-icon {
                transform: rotate(180deg);
            }
            .sidebar-submenu {
                list-style: none;
                padding-left: 0;
                margin-top: 4px;
                margin-bottom: 4px;
                background: #f9fafb;
                border-radius: 10px;
            }
            .sidebar-submenu .sidebar-link {
                padding-left: 44px; /* Indent sub-items */
                font-size: 14px;
                background: transparent;
                color: var(--apple-dark);
            }
            .sidebar-submenu .sidebar-link:hover {
                color: var(--apple-blue);
                background: rgba(0,0,0,0.03);
            }
            .sidebar-submenu .sidebar-link.active {
                color: var(--apple-blue);
                font-weight: 600;
                background: rgba(17, 109, 255, 0.1);
            }
            .expand-icon {
                transition: transform 0.2s;
            }
        </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="{{ url('/dashboard') }}" class="nav-logo">
                <span class="material-symbols-rounded">receipt_long</span>
                Dewey Accounts
            </a>
            
            <ul class="nav-menu">
                @php
                    $userRole = auth()->user()->role->name ?? 'guest';
                    $isAuxiliar = $userRole === 'auxiliar';
                    $canViewReports = in_array($userRole, ['admin_programa', 'administrador', 'tesoreria']);
                @endphp

                @if($isAuxiliar)
                    <li><a href="{{ route('auxiliar.dashboard') }}" class="nav-link">
                        <span class="material-symbols-rounded">dashboard</span>
                        Dashboard
                    </a></li>
                    
                    <li class="nav-dropdown">
                        <a href="#" class="nav-link">
                            <span class="material-symbols-rounded">receipt_long</span>
                            Cuentas
                            <span class="material-symbols-rounded" style="font-size: 18px;">expand_more</span>
                        </a>
                        <div class="nav-dropdown-content">
                            <a href="{{ route('cuentas_cobro.index') }}" class="nav-dropdown-item">
                                <span class="material-symbols-rounded">list</span> Mis Cuentas
                            </a>
                            <a href="{{ route('cuentas_cobro.pagos') }}" class="nav-dropdown-item">
                                <span class="material-symbols-rounded">payments</span> Pagos
                            </a>
                            
                            <!-- Restricted Items (Visible for Popup) -->
                            <div style="border-top: 1px solid #e1e4e8; margin-top: 8px; padding-top: 8px;">
                                <a href="#" onclick="showPermissionError(); return false;" class="nav-dropdown-item" style="color: var(--apple-text-muted);">
                                    <span class="material-symbols-rounded">pie_chart</span> Reportes
                                    <span class="material-symbols-rounded" style="font-size: 14px; margin-left: auto;">lock</span>
                                </a>
                                <a href="#" onclick="showPermissionError(); return false;" class="nav-dropdown-item" style="color: var(--apple-text-muted);">
                                    <span class="material-symbols-rounded">group</span> Usuarios
                                    <span class="material-symbols-rounded" style="font-size: 14px; margin-left: auto;">lock</span>
                                </a>
                                <a href="#" onclick="showPermissionError(); return false;" class="nav-dropdown-item" style="color: var(--apple-text-muted);">
                                    <span class="material-symbols-rounded">admin_panel_settings</span> Roles
                                    <span class="material-symbols-rounded" style="font-size: 14px; margin-left: auto;">lock</span>
                                </a>
                            </div>
                        </div>
                    </li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="nav-link">
                        <span class="material-symbols-rounded">dashboard</span>
                        Dashboard
                    </a></li>
                    <li><a href="{{ route('cuentas_cobro.index') }}" class="nav-link">
                        <span class="material-symbols-rounded">description</span>
                        Cuentas
                    </a></li>
                    <li><a href="{{ route('admin.users.index') }}" class="nav-link">
                        <span class="material-symbols-rounded">group</span>
                        Usuarios
                    </a></li>
                @endif

                <li><a href="{{ route('notificaciones.index') }}" class="nav-link" style="position: relative; display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-rounded" style="font-size: 24px; position: relative;">
                        notifications_active
                        @if(isset($notificacionesNoLeidas) && $notificacionesNoLeidas > 0)
                            <span style="position: absolute; top: -4px; right: -8px; background: #ff3b30; color: white; font-size: 10px; font-weight: 700; padding: 2px 5px; border-radius: 999px; min-width: 18px; text-align: center; border: 2px solid white;">
                                {{ $notificacionesNoLeidas > 99 ? '99+' : $notificacionesNoLeidas }}
                            </span>
                        @endif
                    </span>
                    <span style="display: none;">Notificaciones</span>
                </a></li>
            </ul>

            <div class="nav-actions">
                @auth
                    <div class="user-info">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-apple btn-apple-secondary">
                            <span class="material-symbols-rounded" style="font-size: 18px;">logout</span>
                            Salir
                        </button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="btn-apple">Iniciar sesión</a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Main Layout -->
    <div class="app-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            @php
                $userRole = auth()->user()->role->name ?? 'guest';
                $isAuxiliar = $userRole === 'auxiliar';
                
                // Define permissions based on routes/web.php middleware
                $canViewUsers = in_array($userRole, ['admin_programa']);
                $canViewRoles = in_array($userRole, ['admin_programa']);
                // Removed 'auxiliar' as they don't have permission
                $canViewReports = in_array($userRole, ['admin_programa', 'administrador', 'tesoreria']);
                // Restrict main dashboard for Auxiliar (who has specific dashboard)
                $canViewMainDashboard = !in_array($userRole, ['auxiliar']);
            @endphp

            <div class="sidebar-section">
                <h3 class="sidebar-title">Principal</h3>
                <ul class="sidebar-menu">
                    @if(!$isAuxiliar)
                    <li class="sidebar-item">
                        @if($canViewMainDashboard)
                            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">dashboard</span>
                                Dashboard
                            </a>
                        @else
                            <a href="#" onclick="showPermissionError(); return false;" class="sidebar-link">
                                <span class="material-symbols-rounded">dashboard</span>
                                Dashboard
                            </a>
                        @endif
                    </li>
                    @endif
                    
                    {{-- Dropdown Cuentas de Cobro (Auxiliar & Others) --}}
                    <li class="sidebar-item">
                        <details class="sidebar-dropdown" {{ request()->routeIs('cuentas_cobro.*') || request()->routeIs('auxiliar.*') ? 'open' : '' }}>
                            <summary class="sidebar-link">
                                <span class="material-symbols-rounded">receipt_long</span>
                                <span style="flex:1">Cuentas de Cobro</span>
                                <span class="material-symbols-rounded expand-icon">expand_more</span>
                            </summary>
                            <ul class="sidebar-submenu">
                                @if($isAuxiliar)
                                    <li>
                                        <a href="{{ route('auxiliar.dashboard') }}" class="sidebar-link {{ request()->routeIs('auxiliar.dashboard') ? 'active' : '' }}">
                                            <span class="material-symbols-rounded">dashboard</span>
                                            Dashboard Auxiliar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cuentas_cobro.index') }}" class="sidebar-link {{ request()->routeIs('cuentas_cobro.index') ? 'active' : '' }}">
                                            <span class="material-symbols-rounded">list</span>
                                            Mis Cuentas
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ route('cuentas_cobro.index') }}" class="sidebar-link {{ request()->routeIs('cuentas_cobro.index') ? 'active' : '' }}">
                                            <span class="material-symbols-rounded">list</span>
                                            Listado Cuentas
                                        </a>
                                    </li>
                                @endif
                                
                                <li>
                                    <a href="{{ route('cuentas_cobro.pagos') }}" class="sidebar-link {{ request()->routeIs('cuentas_cobro.pagos') ? 'active' : '' }}">
                                        <span class="material-symbols-rounded">payments</span>
                                        Pagos
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cuentas_cobro.movimientos') }}" class="sidebar-link {{ request()->routeIs('cuentas_cobro.movimientos') ? 'active' : '' }}">
                                        <span class="material-symbols-rounded">table_chart</span>
                                        Movimientos General
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                </ul>
            </div>

            @if(!$isAuxiliar)
            <div class="sidebar-section">
                <h3 class="sidebar-title">Administración</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        @if($canViewUsers)
                            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">group</span>
                                Usuarios
                            </a>
                        @else
                            <a href="#" onclick="showPermissionError(); return false;" class="sidebar-link">
                                <span class="material-symbols-rounded">group</span>
                                Usuarios
                            </a>
                        @endif
                    </li>
                    <li class="sidebar-item">
                        @if($canViewRoles)
                            <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">admin_panel_settings</span>
                                Roles
                            </a>
                        @else
                            <a href="#" onclick="showPermissionError(); return false;" class="sidebar-link">
                                <span class="material-symbols-rounded">admin_panel_settings</span>
                                Roles
                            </a>
                        @endif
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('terceros.index') }}" class="sidebar-link {{ request()->routeIs('terceros.*') ? 'active' : '' }}">
                            <span class="material-symbols-rounded">contacts</span>
                            Terceros / Clientes
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            {{-- Configuración y Consecutivos (Admin Programa) --}}
            @if(in_array($userRole, ['admin_programa']))
            <div class="sidebar-section">
                <h3 class="sidebar-title">Configuración</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        <details class="sidebar-dropdown" {{ request()->routeIs('admin.consecutivos.*') ? 'open' : '' }}>
                            <summary class="sidebar-link">
                                <span class="material-symbols-rounded">123</span>
                                <span style="flex:1">Consecutivos</span>
                                <span class="material-symbols-rounded expand-icon">expand_more</span>
                            </summary>
                            <ul class="sidebar-submenu">
                                <li>
                                    <a href="{{ route('admin.consecutivos.index') }}" class="sidebar-link {{ request()->routeIs('admin.consecutivos.index') ? 'active' : '' }}">
                                        <span class="material-symbols-rounded">list</span>
                                        Ver Consecutivos
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.consecutivos.builder') }}" class="sidebar-link {{ request()->routeIs('admin.consecutivos.builder') ? 'active' : '' }}">
                                        <span class="material-symbols-rounded">build</span>
                                        Planificador Rangos
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.consecutivos.create') }}" class="sidebar-link {{ request()->routeIs('admin.consecutivos.create') ? 'active' : '' }}">
                                        <span class="material-symbols-rounded">add_circle</span>
                                        Nuevo Consecutivo
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('admin.permisos.index') }}" class="sidebar-link {{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}">
                            <span class="material-symbols-rounded">tune</span>
                            Permisos Granulares
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            {{-- Proceso y Seguimiento --}}
            @if(!$isAuxiliar)
            <div class="sidebar-section">
                <h3 class="sidebar-title">Proceso</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        <a href="{{ route('aprobaciones.index') }}" class="sidebar-link {{ request()->routeIs('aprobaciones.*') ? 'active' : '' }}">
                            <span class="material-symbols-rounded">fact_check</span>
                            Aprobaciones
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('cuentas_cobro.seguimiento_general') }}" class="sidebar-link {{ request()->routeIs('cuentas_cobro.seguimiento_general') ? 'active' : '' }}">
                            <span class="material-symbols-rounded">timeline</span>
                            Seguimiento
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('cuentas_cobro.pdfs') }}" class="sidebar-link {{ request()->routeIs('cuentas_cobro.pdfs') ? 'active' : '' }}">
                            <span class="material-symbols-rounded">picture_as_pdf</span>
                            PDFs Generados
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            {{-- DIAN Section (Admin Programa, Tesorería) --}}
            @if(in_array($userRole, ['admin_programa', 'tesoreria', 'administrador']))
            <div class="sidebar-section">
                <h3 class="sidebar-title">DIAN</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        <details class="sidebar-dropdown" {{ request()->routeIs('dian.*') ? 'open' : '' }}>
                            <summary class="sidebar-link">
                                <span class="material-symbols-rounded">verified</span>
                                <span style="flex:1">Facturación DIAN</span>
                                <span class="material-symbols-rounded expand-icon">expand_more</span>
                            </summary>
                            <ul class="sidebar-submenu">
                                <li>
                                    <a href="{{ route('dian.envios') }}" class="sidebar-link {{ request()->routeIs('dian.envios') ? 'active' : '' }}">
                                        <span class="material-symbols-rounded">send</span>
                                        Envíos DIAN
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dian.numeraciones') }}" class="sidebar-link {{ request()->routeIs('dian.numeraciones') ? 'active' : '' }}">
                                        <span class="material-symbols-rounded">tag</span>
                                        Numeraciones
                                    </a>
                                </li>
                                @if(in_array($userRole, ['admin_programa']))
                                <li>
                                    <a href="{{ route('dian.configuracion') }}" class="sidebar-link {{ request()->routeIs('dian.configuracion') ? 'active' : '' }}">
                                        <span class="material-symbols-rounded">settings</span>
                                        Configuración
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </details>
                    </li>
                </ul>
            </div>
            @endif

            @if($canViewReports)
            <div class="sidebar-section">
                <h3 class="sidebar-title">Análisis</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        <a href="{{ route('reportes.index') }}" class="sidebar-link {{ request()->routeIs('reportes.index') ? 'active' : '' }}">
                            <span class="material-symbols-rounded">pie_chart</span>
                            Reportes
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('reportes.devoluciones') }}" class="sidebar-link {{ request()->routeIs('reportes.devoluciones') ? 'active' : '' }}">
                            <span class="material-symbols-rounded">history</span>
                            Devoluciones
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </aside>

        <!-- Sidebar Overlay (for mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" title="Mostrar/Ocultar menú">
        <span class="material-symbols-rounded" id="sidebarToggleIcon">menu</span>
    </button>

    <!-- Permission Denied Modal -->
    <div id="permissionModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:white; border-radius:12px; padding:32px; width:90%; max-width:400px; text-align:center; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
            <div style="width:60px; height:60px; background:#fee2e2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; color:#ef4444;">
                <span class="material-symbols-rounded" style="font-size:32px;">lock</span>
            </div>
            <h3 style="margin-top:0; margin-bottom:8px; color:#162d3d; font-size:20px; font-family: 'Inter', sans-serif; font-weight: 700;">Acceso Restringido</h3>
            <p style="color:#6b7c93; margin-bottom:24px; font-size:15px; line-height: 1.5;">No tienes los permisos necesarios para acceder a esta sección. Contacta al administrador si crees que es un error.</p>
            
            <div style="display:flex; justify-content:center;">
                <button type="button" onclick="closePermissionModal()" style="background:#116dff; color:white; border:none; padding:10px 24px; border-radius:30px; font-weight:600; cursor:pointer; font-size:14px; transition: background 0.2s;">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <script>
        function showPermissionError() {
            document.getElementById('permissionModal').style.display = 'flex';
        }
        function closePermissionModal() {
            document.getElementById('permissionModal').style.display = 'none';
        }
        // Close on click outside
        document.getElementById('permissionModal').addEventListener('click', function(e) {
            if (e.target === this) closePermissionModal();
        });
        
        // Sidebar Toggle functionality
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const icon = document.getElementById('sidebarToggleIcon');
            const isMobile = window.innerWidth < 1024;
            
            if (isMobile) {
                // Mobile: use open class
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
                icon.textContent = sidebar.classList.contains('open') ? 'close' : 'menu';
            } else {
                // Desktop: use collapsed class
                sidebar.classList.toggle('collapsed');
                icon.textContent = sidebar.classList.contains('collapsed') ? 'menu' : 'menu_open';
            }
        }
        
        // Initialize sidebar state based on screen size
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const icon = document.getElementById('sidebarToggleIcon');
            const isMobile = window.innerWidth < 1024;
            
            if (isMobile) {
                sidebar.classList.remove('open');
                icon.textContent = 'menu';
            } else {
                // Default collapsed on desktop for more content space
                sidebar.classList.add('collapsed');
                icon.textContent = 'menu';
            }
        });
        
        // Handle resize
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const icon = document.getElementById('sidebarToggleIcon');
            const isMobile = window.innerWidth < 1024;
            
            if (!isMobile) {
                // Desktop: reset mobile classes
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
        });
    </script>

    @include('components.flash-modal')
    @stack('scripts')
    <!-- Support Widget -->
    <div class="support-widget">
        <button onclick="openSupportModal()" class="support-btn" title="Sugerencias y Soporte">
            <span class="material-symbols-rounded">help</span>
        </button>
    </div>

    <!-- Support Modal -->
    <div id="supportModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h2>Soporte y Sugerencias</h2>
                <span class="custom-close" onclick="closeSupportModal()">&times;</span>
            </div>
            <form action="{{ route('dashboard.sugerencia') }}" method="POST">
                @csrf
                <div class="custom-modal-body">
                    <p class="text-sm text-gray-600 mb-4">Envía tus sugerencias, reportes de error o solicitudes al administrador del sistema.</p>
                    <div class="form-group">
                        <label for="mensaje" style="display:block; margin-bottom:8px; font-weight:500;">Mensaje</label>
                        <textarea name="mensaje" id="mensaje" rows="4" class="form-control" required placeholder="Describe tu solicitud..." style="width:100%; padding:12px; border-radius:8px; border:1px solid #e2e8f0; resize:vertical;"></textarea>
                    </div>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeSupportModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background:var(--apple-blue, #0f172a); color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .support-widget {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }
        .support-btn {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background: var(--apple-blue, #0f172a);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        .support-btn:hover {
            transform: scale(1.1);
        }
        .support-btn span {
            font-size: 1.5rem;
        }

        /* Custom Modal Styles */
        .custom-modal {
            display: none; 
            position: fixed; 
            z-index: 2000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.5); 
            backdrop-filter: blur(4px);
        }

        .custom-modal-content {
            background-color: #fefefe;
            margin: 10% auto; 
            padding: 0;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .custom-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .custom-modal-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
        }

        .custom-close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .custom-close:hover,
        .custom-close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .custom-modal-body {
            padding: 24px;
        }

        .custom-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background-color: #f8fafc;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }
    </style>

    <script>
        function openSupportModal() {
            document.getElementById('supportModal').style.display = 'block';
        }
        function closeSupportModal() {
            document.getElementById('supportModal').style.display = 'none';
        }
        window.onclick = function(event) {
            if (event.target == document.getElementById('supportModal')) {
                closeSupportModal();
            }
        }
    </script>
</body>
</html>
