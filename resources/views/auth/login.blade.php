@extends('layouts.guest')

@section('title', 'Acceso Institucional — SafeRoad SC')

@section('content')
<div class="login-wrapper">
    <div class="login-card">

        <div class="login-logo">
            <div class="login-logo-icon" style="background: linear-gradient(135deg, #0d6e4f, #1e3a5f);">
                <span class="material-symbols-rounded">shield</span>
            </div>
            <h1 class="login-title">SafeRoad SC</h1>
            <p class="login-subtitle">Acceso institucional · Sabana Centro</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <span class="material-symbols-rounded">error</span>
                <div class="alert-content">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">
                    <span class="material-symbols-rounded">mail</span>
                    Correo electrónico
                </label>
                <input type="email"
                       class="form-input @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="email"
                       autofocus
                       placeholder="institucional@correo.com">
                @error('email')
                    <div class="invalid-feedback">
                        <span class="material-symbols-rounded">error</span>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <span class="material-symbols-rounded">lock</span>
                    Contraseña
                </label>
                <input type="password"
                       class="form-input @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       placeholder="Ingresa tu contraseña">
                @error('password')
                    <div class="invalid-feedback">
                        <span class="material-symbols-rounded">error</span>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    Recordarme en este dispositivo
                </label>
            </div>

            <button type="submit" class="btn-login" style="background: linear-gradient(135deg, #0d6e4f, #1e3a5f);">
                <span class="material-symbols-rounded">login</span>
                Iniciar Sesión
            </button>
        </form>

        <div class="login-footer">
            <p class="login-footer-text">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="login-footer-link">Regístrate aquí</a>
            </p>
            <p class="login-footer-text" style="margin-top:8px">
                <a href="{{ route('mapa.index') }}" class="login-footer-link" style="color:#0d6e4f">
                    ← Ver el mapa público sin cuenta
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
