@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Mis Preferencias de Notificación</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('notificaciones.guardarPreferencias') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <h6>Canales de Notificación</h6>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="email_enabled" id="email" {{ $preferencias->email_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="email">Recibir por Correo Electrónico</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="push_enabled" id="push" {{ $preferencias->push_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="push">Notificaciones en el Navegador</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6>Frecuencia de Resumen</h6>
                            <select name="frecuencia_resumen" class="form-select">
                                <option value="inmediato" {{ $preferencias->frecuencia_resumen == 'inmediato' ? 'selected' : '' }}>Inmediato</option>
                                <option value="diario" {{ $preferencias->frecuencia_resumen == 'diario' ? 'selected' : '' }}>Resumen Diario</option>
                                <option value="semanal" {{ $preferencias->frecuencia_resumen == 'semanal' ? 'selected' : '' }}>Resumen Semanal</option>
                                <option value="nunca" {{ $preferencias->frecuencia_resumen == 'nunca' ? 'selected' : '' }}>No enviar resumen</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection