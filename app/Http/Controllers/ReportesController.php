<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\LoteCuentaCobro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportesController extends Controller
{
    /**
     * Dashboard principal de reportes
     */
    public function index()
    {
        $ahora = now();
        
        // === TOTALES GENERALES ===
        $totalCuentas = CuentaCobro::notArchived()->count();
        $totalValor = CuentaCobro::notArchived()->sum('valor_total');
        $totalPagado = CuentaCobro::where('estado_aprobacion', 'pagado')
            ->notArchived()
            ->sum('valor_total');
        $totalPendiente = $totalValor - $totalPagado;

        // === DISTRIBUCIÓN POR ESTADO ===
        $porEstado = CuentaCobro::notArchived()
            ->select('estado_aprobacion', DB::raw('count(*) as total'), DB::raw('sum(valor_total) as valor'))
            ->groupBy('estado_aprobacion')
            ->get();

        // === POR DEPARTAMENTO ===
        $porDepartamento = CuentaCobro::notArchived()
            ->select('departamento', DB::raw('count(*) as total'), DB::raw('sum(valor_total) as valor'))
            ->groupBy('departamento')
            ->orderByDesc('valor')
            ->get();

        // === AGING (Antigüedad de cuentas) ===
        $aging = $this->calcularAging();

        // === CUENTAS MÁS RECIENTES ===
        $recentesCreadas = CuentaCobro::notArchived()
            ->latest('fecha_emision')
            ->limit(10)
            ->get();

        // === CUENTAS PAGADAS ESTE MES ===
        $mesActual = $ahora->month;
        $anoActual = $ahora->year;
        $pagadasEsteMes = CuentaCobro::where('estado_aprobacion', 'pagado')
            ->whereMonth('fecha_pago', $mesActual)
            ->whereYear('fecha_pago', $anoActual)
            ->notArchived()
            ->sum('valor_total');

        return view('reportes.index', compact(
            'totalCuentas',
            'totalValor',
            'totalPagado',
            'totalPendiente',
            'porEstado',
            'porDepartamento',
            'aging',
            'recentesCreadas',
            'pagadasEsteMes'
        ));
    }

    /**
     * Detalle de un departamento
     */
    public function departamento($nombre)
    {
        $departamento = $nombre;
        
        $cuentas = CuentaCobro::where('departamento', $departamento)
            ->notArchived()
            ->with(['user', 'items'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalValor = CuentaCobro::where('departamento', $departamento)
            ->notArchived()
            ->sum('valor_total');

        $distribucion = CuentaCobro::where('departamento', $departamento)
            ->notArchived()
            ->select('estado_aprobacion', DB::raw('count(*) as total'), DB::raw('sum(valor_total) as valor'))
            ->groupBy('estado_aprobacion')
            ->get();

        return view('reportes.departamento', compact('departamento', 'cuentas', 'totalValor', 'distribucion'));
    }

    /**
     * Detalle de un cliente/contratista
     */
    public function cliente($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $cuentas = CuentaCobro::where('user_id', $userId)
            ->notArchived()
            ->with(['items', 'historial'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalValor = CuentaCobro::where('user_id', $userId)
            ->notArchived()
            ->sum('valor_total');

        $totalPagado = CuentaCobro::where('user_id', $userId)
            ->where('estado_aprobacion', 'pagado')
            ->notArchived()
            ->sum('valor_total');

        $distribucion = CuentaCobro::where('user_id', $userId)
            ->notArchived()
            ->select('estado_aprobacion', DB::raw('count(*) as total'), DB::raw('sum(valor_total) as valor'))
            ->groupBy('estado_aprobacion')
            ->get();

        return view('reportes.cliente', compact('user', 'cuentas', 'totalValor', 'totalPagado', 'distribucion'));
    }

    /**
     * Reporte de aging (antigüedad de cuentas)
     */
    public function aging()
    {
        $ahora = now();
        $aging = $this->calcularAging();

        // Detalles de cada bucket
        $buckets = [
            ['rango' => '0-30 días', 'dias_min' => 0, 'dias_max' => 30],
            ['rango' => '31-60 días', 'dias_min' => 31, 'dias_max' => 60],
            ['rango' => '61-90 días', 'dias_min' => 61, 'dias_max' => 90],
            ['rango' => '90+ días', 'dias_min' => 91, 'dias_max' => 999],
        ];

        foreach ($buckets as &$bucket) {
            $bucket['cuentas'] = CuentaCobro::where('estado_aprobacion', 'enviado_cliente')
                ->notArchived()
                ->select('*', DB::raw("DATEDIFF(NOW(), fecha_envio_cliente) as dias_antiguedad"))
                ->havingRaw('dias_antiguedad BETWEEN ? AND ?', [$bucket['dias_min'], $bucket['dias_max']])
                ->with(['user'])
                ->get();
            
            $bucket['total'] = $bucket['cuentas']->count();
            $bucket['valor'] = $bucket['cuentas']->sum('valor_total');
        }

        return view('reportes.aging', compact('buckets'));
    }

    /**
     * Exportar reportes a CSV
     */
    public function exportar($tipo = 'general')
    {
        $filename = 'reporte_' . $tipo . '_' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($tipo) {
            $file = fopen('php://output', 'w');
            
            if ($tipo === 'general') {
                fputcsv($file, ['ID', 'Número', 'Fecha', 'Beneficiario', 'Valor Total', 'Estado', 'Departamento', 'Municipio']);
                CuentaCobro::notArchived()->with(['user'])->chunk(100, function($cuentas) use ($file) {
                    foreach ($cuentas as $c) {
                        fputcsv($file, [
                            $c->id,
                            $c->numero,
                            $c->fecha_emision,
                            $c->nombre_beneficiario,
                            $c->valor_total,
                            $c->estado_aprobacion,
                            $c->departamento,
                            $c->municipio,
                        ]);
                    }
                });
            } elseif ($tipo === 'pagos') {
                fputcsv($file, ['ID', 'Número', 'Beneficiario', 'Valor', 'Fecha Pago', 'Pagado Por']);
                CuentaCobro::where('estado_aprobacion', 'pagado')
                    ->notArchived()
                    ->chunk(100, function($cuentas) use ($file) {
                    foreach ($cuentas as $c) {
                        fputcsv($file, [
                            $c->id,
                            $c->numero,
                            $c->nombre_beneficiario,
                            $c->valor_total,
                            $c->fecha_pago,
                            $c->pagado_por,
                        ]);
                    }
                });
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calcular antigüedad por buckets
     */
    private function calcularAging()
    {
        $ahora = now();
        
        return [
            '0_30' => CuentaCobro::where('estado_aprobacion', 'enviado_cliente')
                ->notArchived()
                ->whereBetween('fecha_envio_cliente', [
                    $ahora->copy()->subDays(30),
                    $ahora
                ])
                ->count(),
            '31_60' => CuentaCobro::where('estado_aprobacion', 'enviado_cliente')
                ->notArchived()
                ->whereBetween('fecha_envio_cliente', [
                    $ahora->copy()->subDays(60),
                    $ahora->copy()->subDays(31)
                ])
                ->count(),
            '61_90' => CuentaCobro::where('estado_aprobacion', 'enviado_cliente')
                ->notArchived()
                ->whereBetween('fecha_envio_cliente', [
                    $ahora->copy()->subDays(90),
                    $ahora->copy()->subDays(61)
                ])
                ->count(),
            '90_plus' => CuentaCobro::where('estado_aprobacion', 'enviado_cliente')
                ->notArchived()
                ->where('fecha_envio_cliente', '<', $ahora->copy()->subDays(90))
                ->count(),
        ];
    }

    /**
     * Reporte consolidado por período
     */
    public function consolidado(Request $request)
    {
        $fechaInicio = $request->filled('fecha_inicio') 
            ? Carbon::parse($request->fecha_inicio) 
            : now()->startOfMonth();
        $fechaFin = $request->filled('fecha_fin') 
            ? Carbon::parse($request->fecha_fin) 
            : now();
        $agruparPor = $request->get('agrupar', 'dia'); // dia, semana, mes

        // Consulta base
        $query = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()]);

        // Totales del período
        $totales = [
            'total_cuentas' => (clone $query)->count(),
            'valor_total' => (clone $query)->sum('valor_total'),
            'pagadas' => (clone $query)->where('estado_aprobacion', 'pagado')->count(),
            'valor_pagado' => (clone $query)->where('estado_aprobacion', 'pagado')->sum('valor_total'),
            'pendientes' => (clone $query)->whereNotIn('estado_aprobacion', ['pagado', 'anulado'])->count(),
            'valor_pendiente' => (clone $query)->whereNotIn('estado_aprobacion', ['pagado', 'anulado'])->sum('valor_total'),
            'anuladas' => (clone $query)->where('estado_aprobacion', 'anulado')->count(),
        ];

        // Agrupar por período
        $formatoSQL = match ($agruparPor) {
            'dia' => "DATE(created_at)",
            'semana' => "YEARWEEK(created_at, 1)",
            'mes' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => "DATE(created_at)"
        };

        $tendencia = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
            ->selectRaw("{$formatoSQL} as periodo, COUNT(*) as total, SUM(valor_total) as valor")
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        // Por estado en el período
        $porEstado = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
            ->select('estado_aprobacion', DB::raw('count(*) as total'), DB::raw('sum(valor_total) as valor'))
            ->groupBy('estado_aprobacion')
            ->get();

        // Por departamento
        $porDepartamento = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
            ->select('departamento', DB::raw('count(*) as total'), DB::raw('sum(valor_total) as valor'))
            ->groupBy('departamento')
            ->orderByDesc('valor')
            ->limit(10)
            ->get();

        // Top beneficiarios
        $topBeneficiarios = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
            ->select('nombre_beneficiario', 'identificacion_beneficiario', 
                DB::raw('count(*) as total_cuentas'), 
                DB::raw('sum(valor_total) as valor_total'))
            ->groupBy('nombre_beneficiario', 'identificacion_beneficiario')
            ->orderByDesc('valor_total')
            ->limit(10)
            ->get();

        // Comparativo con período anterior
        $diasPeriodo = $fechaInicio->diffInDays($fechaFin);
        $periodoAnteriorInicio = $fechaInicio->copy()->subDays($diasPeriodo + 1);
        $periodoAnteriorFin = $fechaInicio->copy()->subDay();

        $comparativo = [
            'anterior' => [
                'cuentas' => CuentaCobro::notArchived()
                    ->whereBetween('created_at', [$periodoAnteriorInicio, $periodoAnteriorFin])
                    ->count(),
                'valor' => CuentaCobro::notArchived()
                    ->whereBetween('created_at', [$periodoAnteriorInicio, $periodoAnteriorFin])
                    ->sum('valor_total'),
            ],
            'actual' => [
                'cuentas' => $totales['total_cuentas'],
                'valor' => $totales['valor_total'],
            ]
        ];

        // Calcular variaciones
        $comparativo['variacion_cuentas'] = $comparativo['anterior']['cuentas'] > 0 
            ? round((($comparativo['actual']['cuentas'] - $comparativo['anterior']['cuentas']) / $comparativo['anterior']['cuentas']) * 100, 1)
            : 0;
        $comparativo['variacion_valor'] = $comparativo['anterior']['valor'] > 0 
            ? round((($comparativo['actual']['valor'] - $comparativo['anterior']['valor']) / $comparativo['anterior']['valor']) * 100, 1)
            : 0;

        return view('reportes.consolidado', compact(
            'fechaInicio', 'fechaFin', 'agruparPor', 'totales', 'tendencia',
            'porEstado', 'porDepartamento', 'topBeneficiarios', 'comparativo'
        ));
    }

    /**
     * Reporte de productividad por usuario
     */
    public function productividad(Request $request)
    {
        $mes = $request->filled('mes') ? Carbon::parse($request->mes . '-01') : now()->startOfMonth();
        $fechaInicio = $mes->copy()->startOfMonth();
        $fechaFin = $mes->copy()->endOfMonth();

        // Productividad por usuario
        $usuarios = User::whereHas('cuentasCobro', function ($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        })
        ->withCount(['cuentasCobro as cuentas_creadas' => function ($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }])
        ->withSum(['cuentasCobro as valor_total' => function ($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }], 'valor_total')
        ->with('role')
        ->orderByDesc('cuentas_creadas')
        ->get();

        // Estadísticas adicionales por usuario
        foreach ($usuarios as $usuario) {
            $cuentasUsuario = CuentaCobro::where('user_id', $usuario->id)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->notArchived();
            
            $usuario->cuentas_pagadas = (clone $cuentasUsuario)->where('estado_aprobacion', 'pagado')->count();
            $usuario->cuentas_rechazadas = (clone $cuentasUsuario)->where('estado_aprobacion', 'rechazado')->count();
            $usuario->tasa_exito = $usuario->cuentas_creadas > 0 
                ? round(($usuario->cuentas_pagadas / $usuario->cuentas_creadas) * 100, 1) 
                : 0;
        }

        // Tendencia diaria del mes
        $tendenciaDiaria = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        return view('reportes.productividad', compact('usuarios', 'mes', 'tendenciaDiaria'));
    }

    /**
     * Exportar a Excel con formato avanzado
     */
    public function exportarExcel(Request $request, $tipo = 'general')
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Estilos
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '116DFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $fechaInicio = $request->filled('fecha_inicio') 
            ? Carbon::parse($request->fecha_inicio) 
            : now()->startOfMonth();
        $fechaFin = $request->filled('fecha_fin') 
            ? Carbon::parse($request->fecha_fin) 
            : now();

        switch ($tipo) {
            case 'consolidado':
                $this->generarExcelConsolidado($sheet, $fechaInicio, $fechaFin, $headerStyle, $dataStyle);
                $filename = 'consolidado_' . $fechaInicio->format('Y-m-d') . '_' . $fechaFin->format('Y-m-d');
                break;
            
            case 'detallado':
                $this->generarExcelDetallado($sheet, $fechaInicio, $fechaFin, $headerStyle, $dataStyle);
                $filename = 'detallado_' . $fechaInicio->format('Y-m-d') . '_' . $fechaFin->format('Y-m-d');
                break;

            case 'pagos':
                $this->generarExcelPagos($sheet, $fechaInicio, $fechaFin, $headerStyle, $dataStyle);
                $filename = 'pagos_' . $fechaInicio->format('Y-m-d') . '_' . $fechaFin->format('Y-m-d');
                break;

            default:
                $this->generarExcelGeneral($sheet, $headerStyle, $dataStyle);
                $filename = 'reporte_general_' . now()->format('Y-m-d');
        }

        // Ajustar anchos automáticamente
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Generar Excel consolidado
     */
    private function generarExcelConsolidado($sheet, $fechaInicio, $fechaFin, $headerStyle, $dataStyle)
    {
        $sheet->setTitle('Consolidado');
        
        // Título
        $sheet->setCellValue('A1', 'REPORTE CONSOLIDADO DE CUENTAS DE COBRO');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $sheet->setCellValue('A2', "Período: {$fechaInicio->format('d/m/Y')} - {$fechaFin->format('d/m/Y')}");
        $sheet->mergeCells('A2:G2');
        
        // Resumen
        $row = 4;
        $sheet->setCellValue("A{$row}", 'RESUMEN');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        
        $totales = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('
                COUNT(*) as total,
                SUM(valor_total) as valor_total,
                SUM(CASE WHEN estado_aprobacion = "pagado" THEN 1 ELSE 0 END) as pagadas,
                SUM(CASE WHEN estado_aprobacion = "pagado" THEN valor_total ELSE 0 END) as valor_pagado
            ')
            ->first();
        
        $row++;
        $sheet->setCellValue("A{$row}", 'Total Cuentas');
        $sheet->setCellValue("B{$row}", $totales->total ?? 0);
        $row++;
        $sheet->setCellValue("A{$row}", 'Valor Total');
        $sheet->setCellValue("B{$row}", $totales->valor_total ?? 0);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('$ #,##0');
        $row++;
        $sheet->setCellValue("A{$row}", 'Cuentas Pagadas');
        $sheet->setCellValue("B{$row}", $totales->pagadas ?? 0);
        $row++;
        $sheet->setCellValue("A{$row}", 'Valor Pagado');
        $sheet->setCellValue("B{$row}", $totales->valor_pagado ?? 0);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('$ #,##0');

        // Por estado
        $row += 2;
        $sheet->setCellValue("A{$row}", 'POR ESTADO');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        
        $row++;
        $sheet->fromArray(['Estado', 'Cantidad', 'Valor'], null, "A{$row}");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($headerStyle);
        
        $porEstado = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->select('estado_aprobacion', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor_total) as valor'))
            ->groupBy('estado_aprobacion')
            ->get();
        
        foreach ($porEstado as $estado) {
            $row++;
            $sheet->setCellValue("A{$row}", ucfirst(str_replace('_', ' ', $estado->estado_aprobacion)));
            $sheet->setCellValue("B{$row}", $estado->total);
            $sheet->setCellValue("C{$row}", $estado->valor);
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('$ #,##0');
        }
    }

    /**
     * Generar Excel detallado
     */
    private function generarExcelDetallado($sheet, $fechaInicio, $fechaFin, $headerStyle, $dataStyle)
    {
        $sheet->setTitle('Detallado');
        
        $headers = ['#', 'Número', 'Fecha', 'Beneficiario', 'Identificación', 'Valor', 'Estado', 'Departamento', 'Municipio', 'Centro Costo'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        $cuentas = CuentaCobro::notArchived()
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at')
            ->get();

        $row = 2;
        foreach ($cuentas as $index => $cuenta) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $cuenta->numero);
            $sheet->setCellValue("C{$row}", $cuenta->fecha_emision?->format('d/m/Y'));
            $sheet->setCellValue("D{$row}", $cuenta->nombre_beneficiario);
            $sheet->setCellValue("E{$row}", $cuenta->identificacion_beneficiario);
            $sheet->setCellValue("F{$row}", $cuenta->valor_total);
            $sheet->setCellValue("G{$row}", ucfirst(str_replace('_', ' ', $cuenta->estado_aprobacion)));
            $sheet->setCellValue("H{$row}", $cuenta->departamento);
            $sheet->setCellValue("I{$row}", $cuenta->municipio);
            $sheet->setCellValue("J{$row}", $cuenta->centroCosto?->nombre);
            
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('$ #,##0');
            $row++;
        }
    }

    /**
     * Generar Excel de pagos
     */
    private function generarExcelPagos($sheet, $fechaInicio, $fechaFin, $headerStyle, $dataStyle)
    {
        $sheet->setTitle('Pagos');
        
        $headers = ['#', 'Número', 'Beneficiario', 'Identificación', 'Valor', 'Fecha Pago', 'Pagado Por', 'Referencia'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        $cuentas = CuentaCobro::where('estado_aprobacion', 'pagado')
            ->notArchived()
            ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_pago')
            ->get();

        $row = 2;
        foreach ($cuentas as $index => $cuenta) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $cuenta->numero);
            $sheet->setCellValue("C{$row}", $cuenta->nombre_beneficiario);
            $sheet->setCellValue("D{$row}", $cuenta->identificacion_beneficiario);
            $sheet->setCellValue("E{$row}", $cuenta->valor_total);
            $sheet->setCellValue("F{$row}", $cuenta->fecha_pago?->format('d/m/Y'));
            $sheet->setCellValue("G{$row}", $cuenta->pagadoPor?->name);
            $sheet->setCellValue("H{$row}", $cuenta->referencia_pago);
            
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('$ #,##0');
            $row++;
        }

        // Total al final
        $sheet->setCellValue("D{$row}", 'TOTAL:');
        $sheet->getStyle("D{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("E{$row}", "=SUM(E2:E" . ($row - 1) . ")");
        $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('$ #,##0');
        $sheet->getStyle("E{$row}")->getFont()->setBold(true);
    }

    /**
     * Generar Excel general
     */
    private function generarExcelGeneral($sheet, $headerStyle, $dataStyle)
    {
        $sheet->setTitle('Reporte General');
        
        $headers = ['ID', 'Número', 'Fecha', 'Beneficiario', 'Valor Total', 'Estado', 'Departamento', 'Municipio'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        $cuentas = CuentaCobro::notArchived()->orderBy('created_at', 'desc')->get();

        $row = 2;
        foreach ($cuentas as $cuenta) {
            $sheet->setCellValue("A{$row}", $cuenta->id);
            $sheet->setCellValue("B{$row}", $cuenta->numero);
            $sheet->setCellValue("C{$row}", $cuenta->fecha_emision?->format('d/m/Y'));
            $sheet->setCellValue("D{$row}", $cuenta->nombre_beneficiario);
            $sheet->setCellValue("E{$row}", $cuenta->valor_total);
            $sheet->setCellValue("F{$row}", ucfirst(str_replace('_', ' ', $cuenta->estado_aprobacion)));
            $sheet->setCellValue("G{$row}", $cuenta->departamento);
            $sheet->setCellValue("H{$row}", $cuenta->municipio);
            
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('$ #,##0');
            $row++;
        }
    }

    /**
     * Dashboard analítico avanzado
     */
    public function analiticas()
    {
        $ahora = now();
        
        // Tendencia últimos 12 meses
        $tendencia12Meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = $ahora->copy()->subMonths($i);
            $tendencia12Meses[] = [
                'mes' => $mes->format('M Y'),
                'creadas' => CuentaCobro::notArchived()
                    ->whereYear('created_at', $mes->year)
                    ->whereMonth('created_at', $mes->month)
                    ->count(),
                'pagadas' => CuentaCobro::notArchived()
                    ->whereYear('fecha_pago', $mes->year)
                    ->whereMonth('fecha_pago', $mes->month)
                    ->where('estado_aprobacion', 'pagado')
                    ->count(),
                'valor_pagado' => CuentaCobro::notArchived()
                    ->whereYear('fecha_pago', $mes->year)
                    ->whereMonth('fecha_pago', $mes->month)
                    ->where('estado_aprobacion', 'pagado')
                    ->sum('valor_total'),
            ];
        }

        // KPIs principales
        $kpis = [
            'tiempo_promedio_pago' => $this->calcularTiempoPromedioPago(),
            'tasa_aprobacion' => $this->calcularTasaAprobacion(),
            'valor_promedio_cuenta' => CuentaCobro::notArchived()->avg('valor_total'),
            'cuentas_mes_actual' => CuentaCobro::notArchived()
                ->whereMonth('created_at', $ahora->month)
                ->whereYear('created_at', $ahora->year)
                ->count(),
        ];

        // Distribución por rango de valores
        $rangosValor = [
            ['label' => '< $500K', 'min' => 0, 'max' => 500000],
            ['label' => '$500K - $1M', 'min' => 500000, 'max' => 1000000],
            ['label' => '$1M - $5M', 'min' => 1000000, 'max' => 5000000],
            ['label' => '$5M - $10M', 'min' => 5000000, 'max' => 10000000],
            ['label' => '> $10M', 'min' => 10000000, 'max' => PHP_INT_MAX],
        ];

        foreach ($rangosValor as &$rango) {
            $rango['count'] = CuentaCobro::notArchived()
                ->whereBetween('valor_total', [$rango['min'], $rango['max']])
                ->count();
        }

        // Top 5 centros de costo
        $topCentrosCosto = CuentaCobro::notArchived()
            ->whereNotNull('centro_costo_id')
            ->select('centro_costo_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor_total) as valor'))
            ->groupBy('centro_costo_id')
            ->with('centroCosto')
            ->orderByDesc('valor')
            ->limit(5)
            ->get();

        return view('reportes.analiticas', compact('tendencia12Meses', 'kpis', 'rangosValor', 'topCentrosCosto'));
    }

    /**
     * Calcular tiempo promedio de pago
     */
    private function calcularTiempoPromedioPago()
    {
        $cuentasPagadas = CuentaCobro::where('estado_aprobacion', 'pagado')
            ->whereNotNull('fecha_pago')
            ->whereNotNull('fecha_emision')
            ->notArchived()
            ->get();

        if ($cuentasPagadas->isEmpty()) {
            return 0;
        }

        $totalDias = $cuentasPagadas->sum(function ($cuenta) {
            return $cuenta->fecha_emision->diffInDays($cuenta->fecha_pago);
        });

        return round($totalDias / $cuentasPagadas->count(), 1);
    }

    /**
     * Calcular tasa de aprobación
     */
    private function calcularTasaAprobacion()
    {
        $total = CuentaCobro::notArchived()->count();
        if ($total === 0) return 0;

        $aprobadas = CuentaCobro::notArchived()
            ->whereIn('estado_aprobacion', ['aprobado', 'enviado_cliente', 'pagado'])
            ->count();

        return round(($aprobadas / $total) * 100, 1);
    }
}
