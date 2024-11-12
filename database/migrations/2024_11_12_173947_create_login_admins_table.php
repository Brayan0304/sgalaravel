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
        Schema::create('login_admins', function (Blueprint $table) {
            $table->string('id')->primary(); // Cambiar id a tipo varchar y establecer como primary key
            $table->string('name'); // Nombre como varchar
            $table->string('email')->unique(); // Correo como varchar y único
            $table->string('direccion'); // Dirección como varchar
            $table->string('telefono'); // Teléfono como varchar
            $table->timestamp('email_verified_at')->nullable(); // Fecha de verificación de email, puede ser null
            $table->string('password'); // Contraseña como varchar
            $table->rememberToken(); // Token de recuerdo
            $table->timestamps(); // Marcas de tiempo created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_admins');
    }
};
