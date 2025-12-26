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
        Schema::table('notificaciones', function (Blueprint $table) {
            // Nuevos campos para notificaciones mejoradas
            $table->string('canal')->default('app')->after('tipo'); // app, email, push
            $table->enum('prioridad', ['baja', 'normal', 'alta', 'urgente'])->default('normal')->after('canal');
            $table->string('categoria')->nullable()->after('prioridad'); // cuenta, lote, tracking, sistema, recordatorio
            $table->json('datos_extra')->nullable()->after('mensaje'); // Datos adicionales para la notificación
            $table->boolean('enviado_email')->default(false);
            $table->timestamp('fecha_envio_email')->nullable();
            $table->boolean('accion_requerida')->default(false);
            $table->string('accion_url')->nullable();
            $table->string('accion_texto')->nullable();
            $table->timestamp('expira_en')->nullable();
            $table->foreignId('relacionado_id')->nullable(); // ID genérico para relacionar
            $table->string('relacionado_tipo')->nullable(); // Tipo de modelo relacionado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropColumn([
                'canal', 'prioridad', 'categoria', 'datos_extra',
                'enviado_email', 'fecha_envio_email', 'accion_requerida',
                'accion_url', 'accion_texto', 'expira_en',
                'relacionado_id', 'relacionado_tipo'
            ]);
        });
    }
};