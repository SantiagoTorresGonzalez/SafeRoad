<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('preferencia_notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Canales de notificación
            $table->boolean('email_habilitado')->default(true);
            $table->boolean('app_habilitado')->default(true);
            
            // Tipos de notificaciones
            $table->boolean('notif_nueva_cuenta')->default(true);
            $table->boolean('notif_cuenta_aprobada')->default(true);
            $table->boolean('notif_cuenta_rechazada')->default(true);
            $table->boolean('notif_cuenta_devuelta')->default(true);
            $table->boolean('notif_cuenta_pagada')->default(true);
            $table->boolean('notif_cuenta_anulada')->default(true);
            $table->boolean('notif_asignacion_rol')->default(true);
            $table->boolean('notif_recordatorios')->default(true);
            $table->boolean('notif_vencimientos')->default(true);
            $table->boolean('notif_actualizaciones_tracking')->default(true);
            $table->boolean('notif_lotes_procesados')->default(true);
            
            // Frecuencia de resúmenes
            $table->enum('frecuencia_resumen', ['inmediato', 'diario', 'semanal', 'nunca'])->default('inmediato');
            $table->time('hora_resumen')->nullable(); // Hora preferida para recibir el resumen
            
            // Horarios de no molestar
            $table->boolean('no_molestar_activo')->default(false);
            $table->time('no_molestar_inicio')->nullable();
            $table->time('no_molestar_fin')->nullable();
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferencia_notificaciones');
    }
};