<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_suscripcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taller_id')->constrained('talleres')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('planes')->nullOnDelete();
            $table->decimal('monto', 10, 2)->default(0);
            $table->date('fecha_pago');
            $table->enum('periodo', ['mensual', 'anual'])->default('mensual');
            $table->string('metodo')->default('transferencia');
            $table->string('referencia')->nullable();
            $table->date('cubre_hasta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_suscripcion');
    }
};
