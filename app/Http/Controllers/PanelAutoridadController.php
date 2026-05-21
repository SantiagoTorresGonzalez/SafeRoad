<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ReporteVial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PanelAutoridadController extends Controller
{
    public function index(Request $request)
    {
        $query = ReporteVial::query()->orderByRaw("
            CASE estado
                WHEN 'pendiente'   THEN 1
                WHEN 'en_atencion' THEN 2
                WHEN 'validado'    THEN 3
                WHEN 'resuelto'    THEN 4
                WHEN 'descartado'  THEN 5
                ELSE 6
            END
        ")->orderBy('created_at', 'desc');

        if ($municipio = $request->input('municipio')) {
            $query->where('municipio', $municipio);
        }
        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }
        if ($tipo = $request->input('tipo_riesgo')) {
            $query->where('tipo_riesgo', $tipo);
        }
        if ($buscar = $request->input('buscar')) {
            $query->where('descripcion', 'ilike', '%' . $buscar . '%');
        }

        $reportes = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => ReporteVial::count(),
            'pendiente'   => ReporteVial::where('estado', 'pendiente')->count(),
            'validado'    => ReporteVial::where('estado', 'validado')->count(),
            'en_atencion' => ReporteVial::where('estado', 'en_atencion')->count(),
            'resuelto'    => ReporteVial::where('estado', 'resuelto')->count(),
        ];

        $municipios = ReporteVial::select('municipio')
            ->distinct()->orderBy('municipio')->pluck('municipio');

        $tipos = ReporteVial::select('tipo_riesgo')
            ->distinct()->orderBy('tipo_riesgo')->pluck('tipo_riesgo');

        return view('panel.index', compact('reportes', 'stats', 'municipios', 'tipos'));
    }

    public function actualizar(Request $request, int $id)
    {
        $request->validate([
            'nuevo_estado'    => ['required', 'in:validado,en_atencion,descartado,resuelto'],
            'notas_autoridad' => ['nullable', 'string', 'max:800'],
        ]);

        $reporte = ReporteVial::findOrFail($id);

        $estadoAnterior = $reporte->estado;
        $estadoNuevo    = $request->input('nuevo_estado');

        $transicionesPermitidas = [
            'pendiente'   => ['validado', 'en_atencion', 'descartado'],
            'validado'    => ['en_atencion', 'descartado'],
            'en_atencion' => ['validado', 'descartado', 'resuelto'],
            'resuelto'    => [],
            'descartado'  => [],
        ];

        if (!in_array($estadoNuevo, $transicionesPermitidas[$estadoAnterior] ?? [])) {
            return back()->with('error', "No es posible cambiar de '{$estadoAnterior}' a '{$estadoNuevo}'.");
        }

        $reporte->estado          = $estadoNuevo;
        $reporte->validado_por    = Auth::id();
        $reporte->validado_at     = now();
        $reporte->notas_autoridad = $request->input('notas_autoridad');
        $reporte->save();

        // ── Auditoría en BD ──────────────────────────────────────────
        AuditLog::estadoCambiado(
            $reporte->id,
            $estadoAnterior,
            $estadoNuevo,
            $request->input('notas_autoridad')
        );

        // ── Log de archivo (respaldo) ────────────────────────────────
        Log::info('[PANEL_AUTORIDAD] Cambio de estado', [
            'reporte_id'   => $reporte->id,
            'estado_nuevo' => $estadoNuevo,
            'usuario_id'   => Auth::id(),
        ]);

        $labels = [
            'validado'    => 'validado',
            'en_atencion' => 'marcado como en atención',
            'descartado'  => 'descartado',
            'resuelto'    => 'marcado como resuelto',
        ];

        return back()->with('success', "Reporte #{$reporte->id} {$labels[$estadoNuevo]} correctamente.");
    }
}
