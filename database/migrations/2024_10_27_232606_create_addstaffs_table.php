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
        Schema::create('addstaffs', function (Blueprint $table) {
            $table->string('id')->primary(); // ID como varchar y clave primaria
            $table->string('name'); // Nombre como varchar
            $table->string('apellidos'); // Apellidos como varchar
            $table->string('email')->unique(); // Correo como varchar y único
            $table->date('fecha_nacimiento'); // Fecha de nacimiento como date
            $table->string('municipio_expedicion'); // Municipio de expedición como varchar
            $table->string('departamento_expedicion'); // Departamento de expedición como varchar
            $table->string('direccion'); // Dirección como varchar
            $table->string('telefono'); // Teléfono como varchar
            $table->string('cargo'); // Cargo como varchar
            $table->rememberToken(); // Token de recuerdo
            $table->timestamps(); // Marcas de tiempo created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addstaffs');
    }
};
