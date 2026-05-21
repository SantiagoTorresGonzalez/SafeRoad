<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteVial;

class PanelAutoridadController extends Controller
{
    public function index(Request $request)
    {
        $query = ReporteVial::query();

        // Filtro por municipio
        if ($request->filled('municipio')) {
            $query->where('municipio', $request->municipio);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $reportes = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'pendientes'  => ReporteVial::where('estado', 'pendiente')->count(),
            'en_atencion' => ReporteVial::where('estado', 'en_atencion')->count(),
            'resueltos'   => ReporteVial::where('estado', 'resuelto')->count(),
            'descartados' => ReporteVial::where('estado', 'descartado')->count(),
        ];

        return view('panel.index', compact('reportes', 'stats'));
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'estado'           => 'required|in:pendiente,en_atencion,resuelto,descartado',
            'notas_autoridad'  => 'nullable|string|max:500',
        ]);

        $reporte = ReporteVial::findOrFail($id);

        $reporte->update([
            'estado'          => $request->estado,
            'notas_autoridad' => $request->notas_autoridad,
            'validado_por'    => auth()->id(),
            'validado_at'     => now(),
        ]);

        return response()->json([
            'message' => 'Reporte actualizado correctamente.',
            'reporte' => $reporte,
        ]);
    }
}