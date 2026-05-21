<?php

namespace App\Http\Controllers;

use App\Models\ReporteVial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanificadorController extends Controller
{
    /**
     * Lista paginada de reportes gestionables por el planificador:
     * solo estados validado, en_atencion y resuelto.
     */
    public function index(Request $request)
    {
        $estadosGestionables = ['validado', 'en_atencion', 'resuelto'];

        $query = ReporteVial::query()
            ->whereIn('estado', $estadosGestionables)
            ->orderByRaw("
                CASE estado
                    WHEN 'validado'    THEN 1
                    WHEN 'en_atencion' THEN 2
                    WHEN 'resuelto'    THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('validado_at', 'asc');

        if ($municipio = $request->input('municipio')) {
            $query->where('municipio', $municipio);
        }
        if ($estado = $request->input('estado')) {
            if (in_array($estado, $estadosGestionables)) {
                $query->where('estado', $estado);
            }
        }
        if ($tipo = $request->input('tipo_riesgo')) {
            $query->where('tipo_riesgo', $tipo);
        }
        if ($buscar = $request->input('buscar')) {
            $query->where('descripcion', 'ilike', '%' . $buscar . '%');
        }

        $reportes = $query->paginate(15)->withQueryString();

        // ── Stats del pipeline ────────────────────────────────────────
        $stats = [
            'validado'          => ReporteVial::where('estado', 'validado')->count(),
            'en_atencion'       => ReporteVial::where('estado', 'en_atencion')->count(),
            'resuelto'          => ReporteVial::where('estado', 'resuelto')->count(),
            'total_gestionables'=> ReporteVial::whereIn('estado', $estadosGestionables)->count(),
        ];

        // ── Valores para los selects ──────────────────────────────────
        $municipios = ReporteVial::whereIn('estado', $estadosGestionables)
            ->select('municipio')->distinct()->orderBy('municipio')->pluck('municipio');

        $tipos = ReporteVial::whereIn('estado', $estadosGestionables)
            ->select('tipo_riesgo')->distinct()->orderBy('tipo_riesgo')->pluck('tipo_riesgo');

        return view('planificador.index', compact('reportes', 'stats', 'municipios', 'tipos'));
    }

    /**
     * Avanzar estado: validado → en_atencion → resuelto.
     */
    public function actualizar(Request $request, int $id)
    {
        $request->validate([
            'nuevo_estado'    => ['required', 'in:en_atencion,resuelto'],
            'notas_autoridad' => ['nullable', 'string', 'max:800'],
        ]);

        $reporte = ReporteVial::findOrFail($id);

        $estadoAnterior = $reporte->estado;
        $estadoNuevo    = $request->input('nuevo_estado');

        // Solo transiciones válidas para este rol
        $transiciones = [
            'validado'    => ['en_atencion'],
            'en_atencion' => ['resuelto'],
        ];

        if (!in_array($estadoNuevo, $transiciones[$estadoAnterior] ?? [])) {
            return back()->with('error', "Transición no permitida: '{$estadoAnterior}' → '{$estadoNuevo}'.");
        }

        $reporte->estado          = $estadoNuevo;
        $reporte->validado_por    = Auth::id();
        $reporte->validado_at     = now();
        $reporte->notas_autoridad = $request->input('notas_autoridad') ?? $reporte->notas_autoridad;
        $reporte->save();

        // ── Log de auditoría ──────────────────────────────────────────
        Log::info('[PLANIFICADOR] Cambio de estado en reporte', [
            'reporte_id'         => $reporte->id,
            'municipio'          => $reporte->municipio,
            'tipo_riesgo'        => $reporte->tipo_riesgo,
            'estado_anterior'    => $estadoAnterior,
            'estado_nuevo'       => $estadoNuevo,
            'notas'              => $request->input('notas_autoridad'),
            'planificador_id'    => Auth::id(),
            'planificador_email' => Auth::user()->email ?? 'N/A',
            'ip'                 => $request->ip(),
            'timestamp'          => now()->toDateTimeString(),
        ]);

        $labels = [
            'en_atencion' => 'marcado como en atención',
            'resuelto'    => 'marcado como resuelto',
        ];

        return back()->with('success', "Reporte #{$reporte->id} {$labels[$estadoNuevo]} correctamente.");
    }
}
