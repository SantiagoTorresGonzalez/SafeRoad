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
        Schema::table('terceros', function (Blueprint $table) {
            if (!Schema::hasColumn('terceros', 'pais_codigo')) {
                $table->string('pais_codigo', 5)->nullable()->default('CO')->after('pais');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            if (Schema::hasColumn('terceros', 'pais_codigo')) {
                $table->dropColumn('pais_codigo');
            }
        });
    }
};
