<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->nullOnDelete();
            $table->foreignId('mecanico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->dateTime('fecha_hora');
            $table->integer('duracion_min')->default(60);
            $table->text('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'confirmada', 'atendida', 'cancelada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
