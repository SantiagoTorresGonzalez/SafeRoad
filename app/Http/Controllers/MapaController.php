<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteVial;
use App\Models\PuntoRiesgo;

class MapaController extends Controller
{
    public function index()
    {
        $reportes = ReporteVial::whereIn('estado', ['en_atencion', 'resuelto'])
            ->select('id', 'tipo_riesgo', 'descripcion', 'latitud', 'longitud', 'municipio', 'estado')
            ->get();

        $puntosRiesgo = PuntoRiesgo::select('id', 'municipio', 'descripcion', 'latitud', 'longitud', 'nivel_riesgo', 'total_muertes', 'anio')
            ->get();

        // Estadísticas de la plataforma
        $stats = [
            'total_reportes'     => ReporteVial::count(),
            'reportes_hoy'       => ReporteVial::whereDate('created_at', today())->count(),
            'pendientes'         => ReporteVial::where('estado', 'pendiente')->count(),
            'resueltos'          => ReporteVial::where('estado', 'resuelto')->count(),
            'en_atencion'        => ReporteVial::where('estado', 'en_atencion')->count(),
            'municipio_top'      => ReporteVial::select('municipio')
                                        ->groupBy('municipio')
                                        ->orderByRaw('COUNT(*) DESC')
                                        ->value('municipio') ?? '—',
            'por_municipio'      => ReporteVial::select('municipio')
                                        ->selectRaw('COUNT(*) as total')
                                        ->groupBy('municipio')
                                        ->orderByRaw('COUNT(*) DESC')
                                        ->get(),
        ];

        return view('mapa.index', compact('reportes', 'puntosRiesgo', 'stats'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'tipo_riesgo' => 'required|string',
            'latitud'     => 'required|numeric',
            'longitud'    => 'required|numeric',
            'municipio'   => 'required|string',
            'descripcion' => 'nullable|string|max:300',
            'foto'        => 'nullable|image|max:5120',
        ]);

        $data = $request->only([
            'tipo_riesgo', 'latitud', 'longitud', 'municipio', 'descripcion'
        ]);

        if ($request->hasFile('foto')) {
        try {
            $data['foto'] = $request->file('foto')->store('reportes', 'public');
        } catch (\Exception $e) {
            \Log::warning('No se pudo guardar foto: ' . $e->getMessage());
            // Continúa sin foto — no bloquea el reporte
        }
    }

        ReporteVial::create($data);

        return response()->json([
            'message' => 'Reporte enviado correctamente. Quedará visible una vez validado por la autoridad municipal.',
        ], 201);
    }
}