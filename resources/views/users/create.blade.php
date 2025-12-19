@extends('layouts.app')

@section('title', 'Crear Usuario - Dewey Accounts')

@section('content')
<style>
    /* Professional Enterprise Design System */
    :root {
        --primary: #116dff;
        --primary-dark: #0056d6;
        --secondary: #0f172a; /* Slate 900 */
        --text-main: #334155; /* Slate 700 */
        --text-light: #64748b; /* Slate 500 */
        --bg-body: #f8fafc; /* Slate 50 */
        --bg-card: #ffffff;
        --border-color: #e2e8f0; /* Slate 200 */
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --radius-md: 12px;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .main-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
        font-family: 'Inter', sans-serif;
    }

    /* Header */
    .page-header {
        margin-bottom: 32px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-light);
        font-size: 14px;
        margin-bottom: 16px;
    }

    .breadcrumb a {
        color: var(--text-light);
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb a:hover { color: var(--primary); }

    .page-title h1 {
        font-size: 28px;
        font-weight: 800;
        color: var(--secondary);
        margin-bottom: 8px;
        letter-spacing: -0.025em;
    }

    .page-subtitle {
        color: var(--text-light);
        font-size: 16px;
    }

    /* Form Card */
    .form-card {
        background: var(--bg-card);
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .form-section {
        padding: 32px;
        border-bottom: 1px solid var(--border-color);
    }

    .form-section:last-child { border-bottom: none; }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--secondary);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-icon {
        width: 32px;
        height: 32px;
        background: #eff6ff;
        color: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--secondary);
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        color: var(--text-main);
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(17, 109, 255, 0.1);
    }

    .form-actions {
        padding: 24px 32px;
        background: #f8fafc;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }
    .btn-primary:hover { background: var(--primary-dark); }

    .btn-secondary {
        background: white;
        border-color: var(--border-color);
        color: var(--text-main);
    }
    .btn-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .error-message {
        color: var(--danger);
        font-size: 12px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="main-container">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="material-symbols-rounded" style="font-size: 16px;">chevron_right</span>
            <a href="{{ route('admin.users.index') }}">Usuarios</a>
            <span class="material-symbols-rounded" style="font-size: 16px;">chevron_right</span>
            <span>Crear Usuario</span>
        </div>
        <div class="page-title">
            <h1>Crear Nuevo Usuario</h1>
            <p class="page-subtitle">Registra un nuevo usuario y asigna sus permisos</p>
        </div>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="form-card">
        @csrf
        
        <!-- Datos Personales -->
        <div class="form-section">
            <div class="section-title">
                <div class="section-icon">
                    <span class="material-symbols-rounded" style="font-size: 20px;">person</span>
                </div>
                Datos Personales
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Ej. Juan Pérez" required>
                    @error('name')
                        <div class="error-message">
                            <span class="material-symbols-rounded" style="font-size: 14px;">error</span> {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="ejemplo@correo.com" required>
                    @error('email')
                        <div class="error-message">
                            <span class="material-symbols-rounded" style="font-size: 14px;">error</span> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Seguridad -->
        <div class="form-section">
            <div class="section-title">
                <div class="section-icon">
                    <span class="material-symbols-rounded" style="font-size: 20px;">security</span>
                </div>
                Seguridad y Acceso
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                    @error('password')
                        <div class="error-message">
                            <span class="material-symbols-rounded" style="font-size: 14px;">error</span> {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Rol del Usuario</label>
                    @php
                        $rolesTraducidos = config('permisos_traducidos.roles', []);
                    @endphp
                    <select name="role_id" class="form-control" required>
                        <option value="">Seleccionar rol...</option>
                        @foreach($roles as $role)
                            @php
                                $rolInfo = $rolesTraducidos[$role->name] ?? null;
                                $rolNombre = $rolInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $role->name));
                            @endphp
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $rolNombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="error-message">
                            <span class="material-symbols-rounded" style="font-size: 14px;">error</span> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <span class="material-symbols-rounded">save</span> Guardar Usuario
            </button>
        </div>
    </form>
</div>
@endsection
