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
        Schema::table('cuentas_cobro', function (Blueprint $table) {
            // Tracking de envío (como remisiones de transporte)
            $table->string('codigo_tracking', 50)->nullable()->unique()->after('numero');
            $table->enum('estado_envio', [
                'no_enviado',
                'preparando',
                'enviado',
                'en_transito',
                'entregado',
                'confirmado',
                'rechazado_destinatario'
            ])->default('no_enviado')->after('estado_aprobacion');
            
            // Información de entrega
            $table->string('metodo_envio', 100)->nullable(); // email, fisico, whatsapp, mixto
            $table->text('direccion_entrega')->nullable();
            $table->string('contacto_entrega', 255)->nullable();
            $table->timestamp('fecha_envio_real')->nullable();
            $table->timestamp('fecha_entrega_estimada')->nullable();
            $table->timestamp('fecha_entrega_real')->nullable();
            
            // Confirmación de recepción
            $table->string('recibido_por', 255)->nullable();
            $table->string('cargo_receptor', 100)->nullable();
            $table->text('firma_recepcion_url')->nullable();
            $table->timestamp('fecha_confirmacion_recepcion')->nullable();
            $table->text('observaciones_recepcion')->nullable();
            
            // Prioridad y seguimiento
            $table->enum('prioridad', ['baja', 'normal', 'alta', 'urgente'])->default('normal');
            $table->boolean('requiere_confirmacion')->default(false);
            $table->integer('dias_para_pago')->nullable();
            $table->integer('recordatorios_enviados')->default(0);
            $table->timestamp('ultimo_recordatorio_at')->nullable();
        });

        // Tabla de eventos de tracking (como movimientos de remisión)
        Schema::create('tracking_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('estado', 50);
            $table->string('ubicacion', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->json('metadata')->nullable(); // IP, dispositivo, etc.
            $table->timestamps();
            
            $table->index(['cuenta_cobro_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_eventos');
        
        Schema::table('cuentas_cobro', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_tracking',
                'estado_envio',
                'metodo_envio',
                'direccion_entrega',
                'contacto_entrega',
                'fecha_envio_real',
                'fecha_entrega_estimada',
                'fecha_entrega_real',
                'recibido_por',
                'cargo_receptor',
                'firma_recepcion_url',
                'fecha_confirmacion_recepcion',
                'observaciones_recepcion',
                'prioridad',
                'requiere_confirmacion',
                'dias_para_pago',
                'recordatorios_enviados',
                'ultimo_recordatorio_at',
            ]);
        });
    }
};
