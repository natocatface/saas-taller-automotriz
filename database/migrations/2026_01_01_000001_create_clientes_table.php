<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento')->default('DNI'); // DNI, RUC, CE, Pasaporte
            $table->string('documento')->nullable();
            $table->string('nombre');
            $table->string('apellidos')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->enum('tipo', ['persona', 'empresa'])->default('persona');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
