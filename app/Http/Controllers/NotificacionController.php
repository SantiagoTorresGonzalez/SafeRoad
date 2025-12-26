<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\PreferenciaNotificacion;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    protected NotificacionService $notificacionService;

    public function __construct(NotificacionService $notificacionService)
    {
        $this->notificacionService = $notificacionService;
    }

    /**
     * Mostrar bandeja de notificaciones del usuario actual.
     */
    public function index(Request $request)
    {
        $query = Notificacion::where('user_id', Auth::id())
            ->with('cuentaCobro');

        // Filtros
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('leida')) {
            $query->where('leida', $request->leida === 'si');
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        $notificaciones = $query->orderByDesc('created_at')->paginate(20);
        $noLeidas = Notificacion::where('user_id', Auth::id())->noLeidas()->count();
        $estadisticas = $this->notificacionService->obtenerEstadisticas(Auth::id());

        return view('notificaciones.index', compact('notificaciones', 'noLeidas', 'estadisticas'));
    }

    /**
     * Marcar una notificación como leída.
     */
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notificacion->marcarComoLeida();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notificación marcada como leída.');
    }

    /**
     * Marcar todas las notificaciones del usuario como leídas.
     */
    public function marcarTodasLeidas()
    {
        Notificacion::where('user_id', Auth::id())
            ->noLeidas()
            ->update([
                'leida' => true,
                'fecha_leida' => now(),
            ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    /**
     * Marcar como leída y redirigir al recurso relacionado.
     */
    public function visitar($id)
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$notificacion->leida) {
            $notificacion->marcarComoLeida();
        }

        // Si hay URL de acción definida, usar esa
        if ($notificacion->accion_url) {
            return redirect($notificacion->accion_url);
        }

        // Redirigir según el tipo de notificación o recurso asociado
        if ($notificacion->cuenta_cobro_id) {
            return redirect()->route('cuentas_cobro.show', $notificacion->cuenta_cobro_id);
        }

        // Si no hay recurso específico, volver atrás
        return back();
    }

    /**
     * Eliminar una notificación
     */
    public function destroy($id)
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notificacion->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notificación eliminada.');
    }

    /**
     * Mostrar preferencias de notificación
     */
    public function preferencias()
    {
        $preferencias = PreferenciaNotificacion::obtenerOCrear(Auth::id());

        return view('notificaciones.preferencias', compact('preferencias'));
    }

    /**
     * Guardar preferencias de notificación
     */
    public function guardarPreferencias(Request $request)
    {
        $request->validate([
            'frecuencia_resumen' => 'required|in:inmediato,diario,semanal,nunca',
            'hora_resumen' => 'nullable|date_format:H:i',
            'no_molestar_inicio' => 'nullable|date_format:H:i',
            'no_molestar_fin' => 'nullable|date_format:H:i',
        ]);

        $preferencias = PreferenciaNotificacion::obtenerOCrear(Auth::id());

        $preferencias->update([
            'email_habilitado' => $request->boolean('email_habilitado'),
            'app_habilitado' => $request->boolean('app_habilitado'),
            'notif_nueva_cuenta' => $request->boolean('notif_nueva_cuenta'),
            'notif_cuenta_aprobada' => $request->boolean('notif_cuenta_aprobada'),
            'notif_cuenta_rechazada' => $request->boolean('notif_cuenta_rechazada'),
            'notif_cuenta_devuelta' => $request->boolean('notif_cuenta_devuelta'),
            'notif_cuenta_pagada' => $request->boolean('notif_cuenta_pagada'),
            'notif_cuenta_anulada' => $request->boolean('notif_cuenta_anulada'),
            'notif_asignacion_rol' => $request->boolean('notif_asignacion_rol'),
            'notif_recordatorios' => $request->boolean('notif_recordatorios'),
            'notif_vencimientos' => $request->boolean('notif_vencimientos'),
            'notif_actualizaciones_tracking' => $request->boolean('notif_actualizaciones_tracking'),
            'notif_lotes_procesados' => $request->boolean('notif_lotes_procesados'),
            'frecuencia_resumen' => $request->frecuencia_resumen,
            'hora_resumen' => $request->hora_resumen,
            'no_molestar_activo' => $request->boolean('no_molestar_activo'),
            'no_molestar_inicio' => $request->no_molestar_inicio,
            'no_molestar_fin' => $request->no_molestar_fin,
        ]);

        return back()->with('success', 'Preferencias guardadas correctamente.');
    }

    /**
     * Obtener conteo de notificaciones no leídas (API)
     */
    public function conteoNoLeidas()
    {
        $count = Notificacion::where('user_id', Auth::id())->noLeidas()->count();
        
        return response()->json([
            'count' => $count,
            'hay_nuevas' => $count > 0,
        ]);
    }

    /**
     * Obtener últimas notificaciones (API para dropdown)
     */
    public function ultimas()
    {
        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->with('cuentaCobro')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'titulo' => $n->titulo,
                    'mensaje' => \Str::limit($n->mensaje, 80),
                    'tipo' => $n->tipo,
                    'prioridad' => $n->prioridad,
                    'leida' => $n->leida,
                    'fecha' => $n->created_at->diffForHumans(),
                    'url' => route('notificaciones.visitar', $n->id),
                    'icono' => $this->obtenerIcono($n->tipo),
                ];
            });

        return response()->json([
            'notificaciones' => $notificaciones,
            'total_no_leidas' => Notificacion::where('user_id', Auth::id())->noLeidas()->count(),
        ]);
    }

    /**
     * Crear recordatorio personalizado
     */
    public function crearRecordatorio(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string|max:1000',
            'fecha_recordatorio' => 'required|date|after:now',
            'cuenta_cobro_id' => 'nullable|exists:cuentas_cobro,id',
        ]);

        $this->notificacionService->crearRecordatorio(
            Auth::id(),
            $request->titulo,
            $request->mensaje,
            new \DateTime($request->fecha_recordatorio),
            $request->cuenta_cobro_id,
            [
                'accion_url' => $request->cuenta_cobro_id 
                    ? route('cuentas_cobro.show', $request->cuenta_cobro_id)
                    : null,
            ]
        );

        return back()->with('success', 'Recordatorio programado correctamente.');
    }

    /**
     * Obtener icono según tipo de notificación
     */
    protected function obtenerIcono(string $tipo): string
    {
        $iconos = [
            'nueva_cuenta' => 'receipt_long',
            'cuenta_aprobada' => 'task_alt',
            'cuenta_rechazada' => 'cancel',
            'cuenta_devuelta' => 'undo',
            'cuenta_pagada' => 'paid',
            'cuenta_anulada' => 'do_not_disturb_on',
            'lote_procesado' => 'layers',
            'lote_creado' => 'library_add',
            'tracking_actualizado' => 'local_shipping',
            'recordatorio' => 'alarm',
            'vencimiento' => 'event_busy',
            'rol_asignado' => 'admin_panel_settings',
            'sistema' => 'info',
        ];

        return $iconos[$tipo] ?? 'notifications';
    }
}
