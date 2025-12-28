@extends('layouts.app')

@section('title', 'Preferencias de Notificación')

@section('content')
<style>
    :root {
        --primary: #116dff;
        --primary-dark: #0056d6;
        --secondary: #0f172a;
        --text-main: #334155;
        --text-light: #64748b;
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --radius-lg: 16px;
        --radius-md: 12px;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .pref-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 24px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--secondary);
        margin: 0 0 8px 0;
        letter-spacing: -0.025em;
    }

    .page-subtitle {
        color: var(--text-light);
        font-size: 16px;
        margin: 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid var(--border-color);
        background: white;
        color: var(--text-main);
        box-shadow: var(--shadow-sm);
    }

    .btn-back:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: var(--secondary);
        transform: translateY(-1px);
        text-decoration: none;
    }

    /* Cards */
    .pref-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .pref-card-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .pref-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pref-card-icon.icon-channels { background: #eff6ff; color: #2563eb; }
    .pref-card-icon.icon-events { background: #ecfdf5; color: #059669; }
    .pref-card-icon.icon-schedule { background: #fef3c7; color: #d97706; }
    .pref-card-icon.icon-dnd { background: #fee2e2; color: #dc2626; }

    .pref-card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--secondary);
        margin: 0 0 4px 0;
    }

    .pref-card-desc {
        font-size: 14px;
        color: var(--text-light);
        margin: 0;
    }

    /* Toggle Switches */
    .toggle-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .toggle-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .toggle-item:hover {
        background: #f1f5f9;
        border-color: var(--border-color);
    }

    .toggle-label {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-label .icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: var(--text-light);
        border: 1px solid var(--border-color);
    }

    .toggle-text h4 {
        font-size: 15px;
        font-weight: 600;
        color: var(--secondary);
        margin: 0 0 2px 0;
    }

    .toggle-text p {
        font-size: 13px;
        color: var(--text-light);
        margin: 0;
    }

    /* Custom Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 28px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: var(--primary);
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    /* Select Dropdown */
    .select-group {
        margin-top: 16px;
    }

    .select-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .custom-select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 15px;
        color: var(--text-main);
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6,9 12,15 18,9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 20px;
        padding-right: 44px;
    }

    .custom-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(17, 109, 255, 0.1);
    }

    /* Time Input */
    .time-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 16px;
    }

    @media (max-width: 500px) {
        .time-grid {
            grid-template-columns: 1fr;
        }
    }

    .time-input-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .time-input-group input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 15px;
        color: var(--text-main);
        transition: all 0.2s;
    }

    .time-input-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(17, 109, 255, 0.1);
    }

    /* Event Types Grid */
    .events-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    @media (max-width: 600px) {
        .events-grid {
            grid-template-columns: 1fr;
        }
    }

    .event-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .event-item:hover {
        background: #f1f5f9;
        border-color: var(--border-color);
    }

    .event-checkbox {
        width: 20px;
        height: 20px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    .event-label {
        font-size: 14px;
        color: var(--text-main);
        cursor: pointer;
        flex: 1;
    }

    /* Submit Button */
    .submit-section {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--border-color);
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        background: var(--primary);
        color: white;
        box-shadow: 0 2px 4px rgba(17, 109, 255, 0.2);
    }

    .btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(17, 109, 255, 0.3);
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 24px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid var(--border-color);
        background: white;
        color: var(--text-main);
    }

    .btn-cancel:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        text-decoration: none;
        color: var(--secondary);
    }

    /* Success Alert */
    .alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: var(--radius-md);
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #065f46;
    }

    .alert-success .icon {
        width: 24px;
        height: 24px;
        color: #10b981;
    }
</style>

<div class="pref-container">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Preferencias de Notificación</h1>
            <p class="page-subtitle">Personaliza cómo y cuándo quieres recibir notificaciones</p>
        </div>
        <a href="{{ route('notificaciones.index') }}" class="btn-back">
            <span class="material-symbols-rounded">arrow_back</span>
            Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <span class="material-symbols-rounded icon">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('notificaciones.guardarPreferencias') }}" method="POST">
        @csrf

        <!-- Canales de Notificación -->
        <div class="pref-card">
            <div class="pref-card-header">
                <div class="pref-card-icon icon-channels">
                    <span class="material-symbols-rounded">send</span>
                </div>
                <div>
                    <h2 class="pref-card-title">Canales de Notificación</h2>
                    <p class="pref-card-desc">Elige cómo quieres recibir tus notificaciones</p>
                </div>
            </div>
            
            <div class="toggle-group">
                <div class="toggle-item">
                    <div class="toggle-label">
                        <div class="icon">
                            <span class="material-symbols-rounded">email</span>
                        </div>
                        <div class="toggle-text">
                            <h4>Correo Electrónico</h4>
                            <p>Recibe notificaciones importantes en tu email</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_habilitado" {{ $preferencias->email_habilitado ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="toggle-item">
                    <div class="toggle-label">
                        <div class="icon">
                            <span class="material-symbols-rounded">notifications</span>
                        </div>
                        <div class="toggle-text">
                            <h4>Notificaciones en la Aplicación</h4>
                            <p>Recibe alertas en tiempo real dentro del sistema</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="app_habilitado" {{ $preferencias->app_habilitado ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Tipos de Eventos -->
        <div class="pref-card">
            <div class="pref-card-header">
                <div class="pref-card-icon icon-events">
                    <span class="material-symbols-rounded">checklist</span>
                </div>
                <div>
                    <h2 class="pref-card-title">Tipos de Eventos</h2>
                    <p class="pref-card-desc">Selecciona qué eventos deseas que te notifiquemos</p>
                </div>
            </div>
            
            <div class="events-grid">
                <label class="event-item">
                    <input type="checkbox" name="notif_nueva_cuenta" class="event-checkbox" {{ ($preferencias->notif_nueva_cuenta ?? true) ? 'checked' : '' }}>
                    <span class="event-label">Nueva cuenta de cobro</span>
                </label>
                <label class="event-item">
                    <input type="checkbox" name="notif_cuenta_aprobada" class="event-checkbox" {{ ($preferencias->notif_cuenta_aprobada ?? true) ? 'checked' : '' }}>
                    <span class="event-label">Cuenta aprobada</span>
                </label>
                <label class="event-item">
                    <input type="checkbox" name="notif_cuenta_rechazada" class="event-checkbox" {{ ($preferencias->notif_cuenta_rechazada ?? true) ? 'checked' : '' }}>
                    <span class="event-label">Cuenta rechazada</span>
                </label>
                <label class="event-item">
                    <input type="checkbox" name="notif_cuenta_devuelta" class="event-checkbox" {{ ($preferencias->notif_cuenta_devuelta ?? true) ? 'checked' : '' }}>
                    <span class="event-label">Cuenta devuelta</span>
                </label>
                <label class="event-item">
                    <input type="checkbox" name="notif_cuenta_pagada" class="event-checkbox" {{ ($preferencias->notif_cuenta_pagada ?? true) ? 'checked' : '' }}>
                    <span class="event-label">Cuenta pagada</span>
                </label>
                <label class="event-item">
                    <input type="checkbox" name="notif_cuenta_anulada" class="event-checkbox" {{ ($preferencias->notif_cuenta_anulada ?? true) ? 'checked' : '' }}>
                    <span class="event-label">Cuenta anulada</span>
                </label>
                <label class="event-item">
                    <input type="checkbox" name="notif_recordatorios" class="event-checkbox" {{ ($preferencias->notif_recordatorios ?? true) ? 'checked' : '' }}>
                    <span class="event-label">Recordatorios</span>
                </label>
                <label class="event-item">
                    <input type="checkbox" name="notif_vencimientos" class="event-checkbox" {{ ($preferencias->notif_vencimientos ?? true) ? 'checked' : '' }}>
                    <span class="event-label">Vencimientos</span>
                </label>
            </div>
        </div>

        <!-- Frecuencia de Resumen -->
        <div class="pref-card">
            <div class="pref-card-header">
                <div class="pref-card-icon icon-schedule">
                    <span class="material-symbols-rounded">schedule</span>
                </div>
                <div>
                    <h2 class="pref-card-title">Frecuencia de Resumen</h2>
                    <p class="pref-card-desc">Configura cuándo recibir el resumen de actividad</p>
                </div>
            </div>
            
            <div class="select-group">
                <label>Frecuencia</label>
                <select name="frecuencia_resumen" class="custom-select">
                    <option value="inmediato" {{ $preferencias->frecuencia_resumen == 'inmediato' ? 'selected' : '' }}>Inmediato - Notificar al instante</option>
                    <option value="diario" {{ $preferencias->frecuencia_resumen == 'diario' ? 'selected' : '' }}>Diario - Un resumen cada día</option>
                    <option value="semanal" {{ $preferencias->frecuencia_resumen == 'semanal' ? 'selected' : '' }}>Semanal - Un resumen cada semana</option>
                    <option value="nunca" {{ $preferencias->frecuencia_resumen == 'nunca' ? 'selected' : '' }}>Nunca - No enviar resúmenes</option>
                </select>
            </div>
            
            <div class="time-grid">
                <div class="time-input-group">
                    <label>Hora preferida para resumen</label>
                    <input type="time" name="hora_resumen" value="{{ $preferencias->hora_resumen ?? '09:00' }}">
                </div>
            </div>
        </div>

        <!-- No Molestar -->
        <div class="pref-card">
            <div class="pref-card-header">
                <div class="pref-card-icon icon-dnd">
                    <span class="material-symbols-rounded">do_not_disturb_on</span>
                </div>
                <div>
                    <h2 class="pref-card-title">Modo No Molestar</h2>
                    <p class="pref-card-desc">Pausa las notificaciones durante ciertos horarios</p>
                </div>
            </div>
            
            <div class="toggle-group">
                <div class="toggle-item">
                    <div class="toggle-label">
                        <div class="icon">
                            <span class="material-symbols-rounded">nights_stay</span>
                        </div>
                        <div class="toggle-text">
                            <h4>Activar No Molestar</h4>
                            <p>No recibirás notificaciones durante el horario configurado</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="no_molestar_activo" id="dnd_toggle" {{ ($preferencias->no_molestar_activo ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            
            <div class="time-grid" id="dnd_times">
                <div class="time-input-group">
                    <label>Hora de inicio</label>
                    <input type="time" name="no_molestar_inicio" value="{{ $preferencias->no_molestar_inicio ?? '22:00' }}">
                </div>
                <div class="time-input-group">
                    <label>Hora de fin</label>
                    <input type="time" name="no_molestar_fin" value="{{ $preferencias->no_molestar_fin ?? '08:00' }}">
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="submit-section">
            <a href="{{ route('notificaciones.index') }}" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn-submit">
                <span class="material-symbols-rounded">save</span>
                Guardar Preferencias
            </button>
        </div>
    </form>
</div>

<script>
    // Toggle DND times visibility
    document.addEventListener('DOMContentLoaded', function() {
        const dndToggle = document.getElementById('dnd_toggle');
        const dndTimes = document.getElementById('dnd_times');
        
        function updateDndVisibility() {
            dndTimes.style.opacity = dndToggle.checked ? '1' : '0.5';
            dndTimes.style.pointerEvents = dndToggle.checked ? 'auto' : 'none';
        }
        
        if (dndToggle && dndTimes) {
            updateDndVisibility();
            dndToggle.addEventListener('change', updateDndVisibility);
        }
    });
</script>
@endsection