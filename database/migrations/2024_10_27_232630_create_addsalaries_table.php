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
        Schema::create('addsalaries', function (Blueprint $table) {
            $table->bigIncrements('id')->primary(); // ID como varchar y clave primaria
            $table->string('id_empleado'); // ID del empleado como varchar
            $table->string('salario'); // Salario como varchar
            $table->string('tiempo_pago'); // Duración de pago como varchar
            $table->timestamps(); // Marcas de tiempo created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addsalaries');
    }
};
