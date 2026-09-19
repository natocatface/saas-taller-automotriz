<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talleres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ruc')->nullable();
            $table->string('contacto_nombre')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('ciudad')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained('planes')->nullOnDelete();
            $table->enum('estado', ['prueba', 'activo', 'suspendido', 'cancelado'])->default('prueba');
            $table->enum('periodo', ['mensual', 'anual'])->default('mensual');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talleres');
    }
};
