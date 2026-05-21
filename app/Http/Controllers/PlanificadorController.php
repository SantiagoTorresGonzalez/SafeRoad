<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ReporteVial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanificadorController extends Controller
{
    private array $estadosGestionables = ['validado', 'en_atencion', 'resuelto'];

    public function index(Request $request)
    {
        $query = ReporteVial::query()
            ->whereIn('estado', $this->estadosGestionables)
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
            if (in_array($estado, $this->estadosGestionables)) {
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

        $stats = [
            'validado'           => ReporteVial::where('estado', 'validado')->count(),
            'en_atencion'        => ReporteVial::where('estado', 'en_atencion')->count(),
            'resuelto'           => ReporteVial::where('estado', 'resuelto')->count(),
            'total_gestionables' => ReporteVial::whereIn('estado', $this->estadosGestionables)->count(),
        ];

        $municipios = ReporteVial::whereIn('estado', $this->estadosGestionables)
            ->select('municipio')->distinct()->orderBy('municipio')->pluck('municipio');

        $tipos = ReporteVial::whereIn('estado', $this->estadosGestionables)
            ->select('tipo_riesgo')->distinct()->orderBy('tipo_riesgo')->pluck('tipo_riesgo');

        return view('planificador.index', compact('reportes', 'stats', 'municipios', 'tipos'));
    }

    public function actualizar(Request $request, int $id)
    {
        $request->validate([
            'nuevo_estado'    => ['required', 'in:en_atencion,resuelto'],
            'notas_autoridad' => ['nullable', 'string', 'max:800'],
        ]);

        $reporte = ReporteVial::findOrFail($id);

        $estadoAnterior = $reporte->estado;
        $estadoNuevo    = $request->input('nuevo_estado');

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

        // ── Auditoría en BD ──────────────────────────────────────────
        AuditLog::estadoCambiado(
            $reporte->id,
            $estadoAnterior,
            $estadoNuevo,
            $request->input('notas_autoridad')
        );

        Log::info('[PLANIFICADOR] Cambio de estado', [
            'reporte_id'   => $reporte->id,
            'estado_nuevo' => $estadoNuevo,
            'usuario_id'   => Auth::id(),
        ]);

        $labels = [
            'en_atencion' => 'marcado como en atención',
            'resuelto'    => 'marcado como resuelto',
        ];

        return back()->with('success', "Reporte #{$reporte->id} {$labels[$estadoNuevo]} correctamente.");
    }
}
