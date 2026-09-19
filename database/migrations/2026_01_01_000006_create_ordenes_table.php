<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // OT-0001
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->foreignId('mecanico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_ingreso');
            $table->date('fecha_entrega')->nullable();
            $table->integer('kilometraje')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['recepcion', 'diagnostico', 'en_proceso', 'esperando_repuestos', 'terminado', 'entregado', 'anulado'])->default('recepcion');
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado_pago', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
