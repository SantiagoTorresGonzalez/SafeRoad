<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\ReporteVial;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CerrarReportesResueltos extends Command
{
    /**
     * Nombre y firma del comando.
     */
    protected $signature = 'app:cerrar-reportes-resueltos
                            {--meses=3      : Meses en estado resuelto antes de cerrar}
                            {--dry-run      : Simula sin hacer cambios reales}
                            {--chunk=100    : Registros procesados por lote}';

    protected $description = 'Archiva reportes que llevan más de N meses en estado "resuelto"';

    /**
     * Estado final al que pasan los reportes archivados.
     * Puedes cambiar esto a 'archivado' si agregas ese estado en el futuro.
     */
    private const ESTADO_DESTINO = 'descartado';

    public function handle(): int
    {
        $meses   = (int) $this->option('meses');
        $dryRun  = (bool) $this->option('dry-run');
        $chunk   = (int) $this->option('chunk');
        $limite  = now()->subMonths($meses);

        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║   SafeRoad SC — Cierre automático de reportes ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');
        $this->line("  Parámetros:");
        $this->line("  · Umbral:   reportes resueltos antes de <fg=yellow>{$limite->format('d/m/Y H:i')}</>");
        $this->line("  · Meses:    <fg=yellow>{$meses}</>");
        $this->line("  · Destino:  <fg=yellow>" . self::ESTADO_DESTINO . "</>");
        $this->line("  · Modo:     " . ($dryRun ? '<fg=cyan>DRY-RUN (sin cambios)</>' : '<fg=green>REAL</>'));
        $this->line("  · Chunk:    <fg=yellow>{$chunk}</> registros por lote");
        $this->info('');

        // ── Contar afectados ─────────────────────────────────────────
        $total = ReporteVial::where('estado', 'resuelto')
            ->where('validado_at', '<', $limite)
            ->count();

        if ($total === 0) {
            $this->info('  ✓ No hay reportes que cumplan el criterio. Nada que hacer.');
            $this->info('');
            return self::SUCCESS;
        }

        $this->line("  Reportes encontrados: <fg=yellow>{$total}</>");
        $this->info('');

        if ($dryRun) {
            $this->warn("  [DRY-RUN] Se habrían cerrado {$total} reportes. Sin cambios aplicados.");
            $this->info('');
            return self::SUCCESS;
        }

        // ── Confirmación interactiva (solo en terminal) ───────────────
        if ($this->input->isInteractive()) {
            if (!$this->confirm("  ¿Confirmas cerrar {$total} reportes?", false)) {
                $this->warn('  Operación cancelada por el usuario.');
                return self::SUCCESS;
            }
        }

        // ── Procesar en lotes ────────────────────────────────────────
        $procesados = 0;
        $errores    = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% · %elapsed:6s% transcurrido');
        $bar->start();

        ReporteVial::where('estado', 'resuelto')
            ->where('validado_at', '<', $limite)
            ->orderBy('id')
            ->chunk($chunk, function ($reportes) use (&$procesados, &$errores, $bar, $meses) {
                foreach ($reportes as $reporte) {
                    try {
                        $estadoAnterior = $reporte->estado;

                        $reporte->estado          = self::ESTADO_DESTINO;
                        $reporte->notas_autoridad = trim(
                            ($reporte->notas_autoridad ? $reporte->notas_autoridad . ' | ' : '') .
                            "Cerrado automáticamente por comando app:cerrar-reportes-resueltos " .
                            "(+{$meses} meses en estado resuelto). " . now()->format('d/m/Y H:i')
                        );
                        $reporte->save();

                        // Auditoría en BD
                        AuditLog::estadoCambiado(
                            $reporte->id,
                            $estadoAnterior,
                            self::ESTADO_DESTINO,
                            "Cierre automático: más de {$meses} meses en estado resuelto"
                        );

                        $procesados++;
                    } catch (\Throwable $e) {
                        $errores++;
                        Log::error('[CIERRE_AUTO] Error al cerrar reporte', [
                            'reporte_id' => $reporte->id,
                            'error'      => $e->getMessage(),
                        ]);
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->info('');
        $this->info('');

        // ── Resumen ──────────────────────────────────────────────────
        $this->info('  Resultado:');
        $this->line("  · <fg=green>✓ Cerrados correctamente:</> {$procesados}");

        if ($errores > 0) {
            $this->line("  · <fg=red>✕ Errores:</> {$errores} (ver storage/logs/laravel.log)");
        }

        // Log de resumen en archivo
        Log::info('[CIERRE_AUTO] Ejecución completada', [
            'umbral_meses' => $meses,
            'total_encontrados' => $total,
            'procesados'        => $procesados,
            'errores'           => $errores,
            'timestamp'         => now()->toDateTimeString(),
        ]);

        // Auditoría global del comando
        AuditLog::registrar(
            accion:      'cierre_automatico',
            entidad:     'ReporteVial',
            datos:       [
                'umbral_meses'      => $meses,
                'total_encontrados' => $total,
                'procesados'        => $procesados,
                'errores'           => $errores,
            ],
            descripcion: "Cierre automático: {$procesados} reportes cerrados (umbral {$meses} meses)",
        );

        $this->info('');
        return $errores > 0 ? self::FAILURE : self::SUCCESS;
    }
}
