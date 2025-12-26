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
        // Plantillas de flujo de trabajo
        Schema::create('plantillas_flujo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('es_predeterminado')->default(false);
            $table->boolean('activo')->default(true);
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Etapas del flujo de trabajo
        Schema::create('etapas_flujo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_flujo_id')->constrained('plantillas_flujo')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('codigo'); // borrador, en_revision, aprobado, etc.
            $table->integer('orden');
            $table->string('rol_responsable')->nullable(); // Rol que puede actuar en esta etapa
            $table->string('color')->default('#6c757d');
            $table->string('icono')->default('pending');
            
            // SLA (Service Level Agreement)
            $table->integer('sla_horas')->nullable(); // Tiempo máximo en horas
            $table->boolean('sla_activo')->default(false);
            
            // Acciones permitidas
            $table->boolean('puede_aprobar')->default(false);
            $table->boolean('puede_rechazar')->default(false);
            $table->boolean('puede_devolver')->default(false);
            $table->boolean('puede_editar')->default(false);
            $table->boolean('requiere_comentario')->default(false);
            
            // Notificaciones
            $table->boolean('notificar_entrada')->default(true);
            $table->boolean('notificar_salida')->default(true);
            $table->boolean('notificar_sla_vencido')->default(true);
            
            $table->timestamps();
            
            $table->unique(['plantilla_flujo_id', 'codigo']);
            $table->unique(['plantilla_flujo_id', 'orden']);
        });

        // Transiciones permitidas entre etapas
        Schema::create('transiciones_flujo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_flujo_id')->constrained('plantillas_flujo')->cascadeOnDelete();
            $table->foreignId('etapa_origen_id')->constrained('etapas_flujo')->cascadeOnDelete();
            $table->foreignId('etapa_destino_id')->constrained('etapas_flujo')->cascadeOnDelete();
            $table->string('accion'); // aprobar, rechazar, devolver, etc.
            $table->string('etiqueta'); // Texto del botón
            $table->string('color_boton')->default('primary');
            $table->string('icono')->default('arrow_forward');
            $table->boolean('requiere_comentario')->default(false);
            $table->boolean('activa')->default(true);
            $table->timestamps();
            
            $table->unique(['plantilla_flujo_id', 'etapa_origen_id', 'accion'], 'trans_flujo_plantilla_origen_accion_unique');
        });

        // Reglas de asignación automática
        Schema::create('reglas_asignacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_flujo_id')->constrained('plantillas_flujo')->cascadeOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('prioridad')->default(100);
            $table->boolean('activa')->default(true);
            
            // Condiciones (JSON con criterios)
            $table->json('condiciones')->nullable();
            // Ejemplo: {"valor_min": 1000000, "departamento": "Antioquia", "centro_costo_id": 5}
            
            // Acciones
            $table->string('asignar_a_tipo'); // usuario, rol, grupo
            $table->unsignedBigInteger('asignar_a_id')->nullable(); // ID del usuario/rol
            $table->foreignId('etapa_inicial_id')->nullable()->constrained('etapas_flujo')->nullOnDelete();
            
            $table->timestamps();
        });

        // Historial de cambios de etapa con SLA
        Schema::create('historial_flujo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->cascadeOnDelete();
            $table->foreignId('etapa_id')->nullable()->constrained('etapas_flujo')->nullOnDelete();
            $table->string('etapa_codigo');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion'); // entrada, salida, escalado, sla_vencido
            $table->text('comentario')->nullable();
            $table->integer('tiempo_en_etapa')->nullable(); // Minutos
            $table->boolean('sla_cumplido')->nullable();
            $table->timestamp('fecha_hora');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_flujo');
        Schema::dropIfExists('reglas_asignacion');
        Schema::dropIfExists('transiciones_flujo');
        Schema::dropIfExists('etapas_flujo');
        Schema::dropIfExists('plantillas_flujo');
    }
};