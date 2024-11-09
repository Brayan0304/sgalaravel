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
        Schema::create('reports', function (Blueprint $table) {
            $table->string('id')->primary(); // ID como varchar y clave primaria
            $table->string('titulo'); // Título como varchar
            $table->string('titulo_2'); // Título 2 como varchar
            $table->string('parrafo'); // Párrafo como varchar
            $table->string('expedicion'); // Expedición como varchar
            $table->string('tamano_letra_titulo'); // Tamaño de letra del título como varchar
            $table->string('tamano_letra_titulo_2'); // Tamaño de letra del título 2 como varchar
            $table->string('tamano_letra_parrafo'); // Tamaño de letra del párrafo como varchar
            $table->string('tamano_letra_expedicion'); // Tamaño de letra de expedición como varchar
            $table->string('margen_izquierdo'); // Margen izquierdo como varchar
            $table->string('margen_derecho'); // Margen derecho como varchar
            $table->string('margen_superior'); // Margen superior como varchar
            $table->string('margen_inferior'); // Margen inferior como varchar
            $table->string('tamano_hoja'); // Tamaño de hoja como varchar
            $table->string('estilo_letra_titulo'); // Estilo de letra del título como varchar
            $table->string('estilo_letra_titulo_2'); // Estilo de letra del título 2 como varchar
            $table->string('estilo_parrafo'); // Estilo de letra del párrafo como varchar
            $table->string('estilo_expedicion'); // Estilo de letra de expedición como varchar
            $table->timestamps(); // Marcas de tiempo created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
