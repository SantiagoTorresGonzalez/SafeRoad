<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ReporteVial;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalistaController extends Controller
{
    private array $estadosVisibles = ['validado', 'en_atencion', 'resuelto'];

    public function index(Request $request)
    {
        $query = ReporteVial::query()
            ->whereIn('estado', $this->estadosVisibles)
            ->orderBy('created_at', 'desc');

        $query = $this->aplicarFiltros($query, $request);

        $reportes = $query->paginate(20)->withQueryString();

        $stats = [
            'total'       => ReporteVial::whereIn('estado', $this->estadosVisibles)->count(),
            'validado'    => ReporteVial::where('estado', 'validado')->count(),
            'en_atencion' => ReporteVial::where('estado', 'en_atencion')->count(),
            'resuelto'    => ReporteVial::where('estado', 'resuelto')->count(),
            'municipios'  => ReporteVial::whereIn('estado', $this->estadosVisibles)
                                ->distinct('municipio')->count('municipio'),
        ];

        $municipios = ReporteVial::whereIn('estado', $this->estadosVisibles)
            ->select('municipio')->distinct()->orderBy('municipio')->pluck('municipio');

        $tipos = ReporteVial::whereIn('estado', $this->estadosVisibles)
            ->select('tipo_riesgo')->distinct()->orderBy('tipo_riesgo')->pluck('tipo_riesgo');

        // ── Auditoría: registrar consulta ────────────────────────────
        AuditLog::registrar(
            accion:      'consulta_reportes',
            entidad:     'ReporteVial',
            datos:       array_merge(
                $request->only(['municipio', 'estado', 'tipo_riesgo', 'fecha_desde', 'fecha_hasta']),
                ['total_resultados' => $reportes->total()]
            ),
            descripcion: 'Analista consultó listado de reportes',
        );

        return view('analista.index', compact('reportes', 'stats', 'municipios', 'tipos'));
    }

    public function exportar(Request $request): StreamedResponse
    {
        $query = ReporteVial::query()
            ->whereIn('estado', $this->estadosVisibles)
            ->orderBy('created_at', 'desc');

        $query = $this->aplicarFiltros($query, $request);
        $reportes = $query->get();

        // ── Auditoría: registrar exportación ─────────────────────────
        AuditLog::registrar(
            accion:      'exportar_csv',
            entidad:     'ReporteVial',
            datos:       array_merge(
                $request->only(['municipio', 'estado', 'tipo_riesgo', 'fecha_desde', 'fecha_hasta']),
                ['total_exportados' => $reportes->count()]
            ),
            descripcion: "Analista exportó {$reportes->count()} reportes a CSV",
        );

        $filename = 'saferoad_reportes_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($reportes) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID', 'Municipio', 'Tipo de riesgo', 'Descripción',
                'Estado', 'Latitud', 'Longitud', 'Notas autoridad',
                'Fecha registro', 'Fecha validación',
            ]);

            foreach ($reportes as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->municipio,
                    $r->tipo_riesgo,
                    $r->descripcion,
                    $r->estado,
                    $r->latitud,
                    $r->longitud,
                    $r->notas_autoridad ?? '',
                    $r->created_at->format('d/m/Y H:i'),
                    $r->validado_at
                        ? \Carbon\Carbon::parse($r->validado_at)->format('d/m/Y H:i')
                        : '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function aplicarFiltros($query, Request $request)
    {
        if ($v = $request->input('municipio'))   $query->where('municipio', $v);
        if ($v = $request->input('tipo_riesgo')) $query->where('tipo_riesgo', $v);
        if ($v = $request->input('estado')) {
            if (in_array($v, $this->estadosVisibles)) $query->where('estado', $v);
        }
        if ($v = $request->input('fecha_desde')) $query->whereDate('created_at', '>=', $v);
        if ($v = $request->input('fecha_hasta')) $query->whereDate('created_at', '<=', $v);

        return $query;
    }
}
