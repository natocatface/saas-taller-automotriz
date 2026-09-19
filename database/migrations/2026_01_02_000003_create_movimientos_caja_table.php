<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['ingreso', 'egreso'])->default('ingreso');
            $table->string('concepto');
            $table->decimal('monto', 12, 2)->default(0);
            $table->date('fecha');
            $table->string('metodo_pago')->default('efectivo'); // efectivo, tarjeta, transferencia, yape/plin
            $table->foreignId('orden_id')->nullable()->constrained('ordenes')->nullOnDelete();
            $table->foreignId('comprobante_id')->nullable()->constrained('comprobantes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
