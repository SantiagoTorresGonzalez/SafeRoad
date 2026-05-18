<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_viales', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_riesgo');
            $table->text('descripcion')->nullable();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->string('municipio');
            $table->string('foto')->nullable();
            $table->enum('estado', [
                'pendiente',
                'en_atencion',
                'resuelto',
                'descartado'
            ])->default('pendiente');
            $table->foreignId('validado_por')->nullable()->constrained('users');
            $table->timestamp('validado_at')->nullable();
            $table->text('notas_autoridad')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_viales');
    }
};