<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puntos_riesgo', function (Blueprint $table) {
            $table->id();
            $table->string('municipio');
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->string('descripcion')->nullable();
            $table->integer('total_muertes')->default(0);
            $table->year('anio');
            $table->enum('nivel_riesgo', [
                'alto',
                'medio',
                'bajo'
            ])->default('alto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puntos_riesgo');
    }
};