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
        Schema::table('addstaffs', function (Blueprint $table) {
            if (!Schema::hasColumn('addstaffs', 'fecha_ingreso')) {
                $table->date('fecha_ingreso')->nullable()->after('cargo');
            }
            if (!Schema::hasColumn('addstaffs', 'fecha_salida')) {
                $table->date('fecha_salida')->nullable()->after('fecha_ingreso');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addstaffs', function (Blueprint $table) {
            if (Schema::hasColumn('addstaffs', 'fecha_salida')) {
                $table->dropColumn('fecha_salida');
            }
            if (Schema::hasColumn('addstaffs', 'fecha_ingreso')) {
                $table->dropColumn('fecha_ingreso');
            }
        });
    }
};
