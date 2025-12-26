@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<style>
    :root {
        --apple-blue: #0071e3;
        --apple-dark: #1d1d1f;
        --apple-gray: #86868b;
        --apple-light-gray: #f5f5f7;
        --apple-green: #30d158;
        --apple-red: #ff3b30;
    }

    .form-breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
        color: var(--apple-gray);
        font-size: 0.9rem;
    }

    .form-breadcrumb a {
        color: var(--apple-blue);
        text-decoration: none;
        transition: opacity 0.3s ease;
    }

    .form-breadcrumb a:hover {
        opacity: 0.7;
    }

    .form-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .form-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: fadeInUp 0.6s ease;
    }

    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .form-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    }

    .form-header-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    .form-header h1 {
        color: white;
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .form-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0;
        font-size: 1rem;
    }

    .form-body {
        padding: 3rem 2.5rem;
    }

    .form-sections {
        display: grid;
        gap: 3rem;
    }

    .form-section {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 2rem;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--apple-dark);
        margin-bottom: 2rem;
    }

    .form-section-title .material-symbols-rounded {
        font-size: 1.8rem;
        color: var(--apple-blue);
    }

    .user-info-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: var(--apple-light-gray);
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 700;
    }

    .user-info-details h4 {
        margin: 0 0 4px 0;
        color: var(--apple-dark);
        font-weight: 600;
    }

    .user-info-details p {
        margin: 0;
        color: var(--apple-gray);
        font-size: 0.9rem;
    }

    .alert-errors {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
        border-left: 4px solid #ff3b30;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        animation: slideInDown 0.4s ease;
    }

    .alert-errors-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #ff3b30;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .alert-errors-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .alert-errors-list li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #d32f2f;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--apple-dark);
        margin-bottom: 0.75rem;
    }

    .form-label .material-symbols-rounded {
        font-size: 1.2rem;
        color: var(--apple-blue);
    }

    .form-input-wrapper {
        position: relative;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        font-size: 1rem;
        border: 2px solid #e5e5e7;
        border-radius: 12px;
        outline: none;
        transition: all 0.3s ease;
        background: var(--apple-light-gray);
        color: var(--apple-dark);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .form-input:focus,
    .form-select:focus {
        border-color: var(--apple-blue);
        background: white;
        box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
    }

    .form-input.is-invalid,
    .form-select.is-invalid {
        border-color: #ff3b30;
        background: #fff5f5;
    }

    .form-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--apple-gray);
        font-size: 1.3rem;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    .form-input:focus ~ .form-icon,
    .form-select:focus ~ .form-icon {
        color: var(--apple-blue);
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--apple-gray);
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: var(--apple-blue);
    }

    .info-box {
        background: rgba(0, 113, 227, 0.1);
        border-left: 4px solid var(--apple-blue);
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        font-size: 0.95rem;
        color: var(--apple-blue);
    }

    .info-box .material-symbols-rounded {
        font-size: 1.3rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e5e7;
    }

    .btn-cancel,
    .btn-submit,
    .btn-danger-alt {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
    }

    .btn-cancel {
        background: var(--apple-light-gray);
        color: var(--apple-dark);
    }

    .btn-cancel:hover {
        background: #e0e0e2;
        transform: translateY(-2px);
    }

    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-submit:hover {
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        transform: translateY(-2px);
    }

    .btn-danger-alt {
        background: rgba(255, 59, 48, 0.15);
        color: var(--apple-red);
    }

    .btn-danger-alt:hover {
        background: var(--apple-red);
        color: white;
        transform: translateY(-2px);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-container {
            padding: 1rem;
        }

        .form-body {
            padding: 2rem 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-cancel,
        .btn-submit,
        .btn-danger-alt {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="form-container">
    <div class="form-breadcrumb">
        <a href="{{ route('admin.users.index') }}">Usuarios</a>
        <span class="material-symbols-rounded" style="font-size: 1rem;">chevron_right</span>
        <span>{{ $user->name }}</span>
    </div>

    <div class="form-card">
        <div class="form-header">
            <span class="material-symbols-rounded form-header-icon">edit_note</span>
            <h1>Editar Usuario</h1>
            <p>Actualiza la información del usuario: <strong>{{ $user->name }}</strong></p>
        </div>

        <div class="form-body">
            <div class="form-sections">
                <div class="user-info-card">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="user-info-details">
                        <h4>{{ $user->name }}</h4>
                        <p>{{ $user->email }}</p>
                        <p style="font-size: 0.8rem; margin-top: 4px;">
                            Creado: {{ $user->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert-errors">
                        <div class="alert-errors-title">
                            <span class="material-symbols-rounded">report</span>
                            <strong>Se encontraron los siguientes errores:</strong>
                        </div>
                        <ul class="alert-errors-list">
                            @foreach ($errors->all() as $error)
                                <li>
                                    <span class="material-symbols-rounded">arrow_right</span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h3 class="form-section-title">
                            <span class="material-symbols-rounded">person</span>
                            Información Personal
                        </h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <span class="material-symbols-rounded">badge</span>
                                    Nombre completo
                                </label>
                                <div class="form-input-wrapper">
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name"
                                        class="form-input @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" 
                                        placeholder="Juan Pérez García"
                                        required
                                    >
                                    <span class="material-symbols-rounded form-icon">person</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <span class="material-symbols-rounded">alternate_email</span>
                                    Correo electrónico
                                </label>
                                <div class="form-input-wrapper">
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="email"
                                        class="form-input @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" 
                                        placeholder="ejemplo@correo.com"
                                        required
                                    >
                                    <span class="material-symbols-rounded form-icon">mail</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">
                            <span class="material-symbols-rounded">shield_person</span>
                            Rol y Permisos
                        </h3>

                        @php
                            $rolesTraducidos = config('permisos_traducidos.roles', []);
                            $rolActualInfo = $rolesTraducidos[$user->role?->name ?? ''] ?? null;
                            $rolActualNombre = $rolActualInfo['nombre_es'] ?? ($user->role?->name ?? 'Sin rol asignado');
                        @endphp
                        <div class="info-box">
                            <span class="material-symbols-rounded">info</span>
                            <div>Rol actual: <strong>{{ $rolActualNombre }}</strong></div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="role_id" class="form-label">
                                    <span class="material-symbols-rounded">workspace_premium</span>
                                    Selecciona un rol
                                </label>
                                <div class="form-input-wrapper">
                                    <select 
                                        name="role_id" 
                                        id="role_id"
                                        class="form-select @error('role_id') is-invalid @enderror"
                                        required
                                    >
                                        <option value="">Selecciona un rol</option>
                                        @foreach ($roles as $role)
                                            @php
                                                $rolInfo = $rolesTraducidos[$role->name] ?? null;
                                                $rolNombre = $rolInfo['nombre_es'] ?? ucfirst(str_replace('_', ' ', $role->name));
                                            @endphp
                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $rolNombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-rounded form-icon">shield_person</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">
                            <span class="material-symbols-rounded">lock</span>
                            Seguridad
                        </h3>

                        <div class="info-box">
                            <span class="material-symbols-rounded">info</span>
                            <div>Dejar vacío para mantener la contraseña actual</div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="password" class="form-label">
                                    <span class="material-symbols-rounded">key</span>
                                    Nueva contraseña (opcional)
                                </label>
                                <div class="form-input-wrapper">
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password"
                                        class="form-input @error('password') is-invalid @enderror"
                                        placeholder="Mínimo 8 caracteres"
                                    >
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <span class="material-symbols-rounded">visibility</span>
                                    </button>
                                    <span class="material-symbols-rounded form-icon">lock</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                            <span class="material-symbols-rounded">close</span>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-submit">
                            <span class="material-symbols-rounded">save</span>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = event.target.closest('button');
        
        if (input.type === 'password') {
            input.type = 'text';
            button.innerHTML = '<span class="material-symbols-rounded">visibility_off</span>';
        } else {
            input.type = 'password';
            button.innerHTML = '<span class="material-symbols-rounded">visibility</span>';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editUserForm');
        const inputs = form.querySelectorAll('.form-input, .form-select');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid') && this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });

        const emailInput = document.getElementById('email');
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            }
        });

        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0 && this.value.length < 8) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });
</script>

@endsection