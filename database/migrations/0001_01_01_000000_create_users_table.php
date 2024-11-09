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
        Schema::create('users', function (Blueprint $table) {
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

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
