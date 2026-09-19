<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('placa')->index();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->year('anio')->nullable();
            $table->string('color')->nullable();
            $table->string('vin')->nullable();
            $table->string('motor')->nullable();
            $table->enum('combustible', ['gasolina', 'diesel', 'glp', 'gnv', 'electrico', 'hibrido'])->default('gasolina');
            $table->enum('transmision', ['manual', 'automatica'])->nullable();
            $table->integer('kilometraje')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
