<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Quién hizo la acción
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Qué hizo
            $table->string('accion', 80);          // e.g. "estado_cambiado", "exportar_csv"
            $table->string('descripcion')->nullable(); // texto libre legible

            // Sobre qué entidad
            $table->string('entidad', 80)->nullable();  // e.g. "ReporteVial"
            $table->unsignedBigInteger('entidad_id')->nullable();

            // Datos extra (JSON flexible)
            $table->jsonb('datos')->nullable();        // estado anterior/nuevo, filtros, etc.

            // Contexto de red
            $table->string('ip', 45)->nullable();      // soporta IPv6
            $table->string('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Índices útiles para consultas en el panel
            $table->index('user_id');
            $table->index('accion');
            $table->index(['entidad', 'entidad_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
