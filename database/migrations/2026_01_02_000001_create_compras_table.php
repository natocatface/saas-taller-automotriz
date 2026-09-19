<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // CMP-0001
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->string('tipo_documento')->default('factura'); // factura, boleta, guia
            $table->string('documento_ref')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['registrada', 'anulada'])->default('registrada');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('compra_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->nullable()->constrained('repuestos')->nullOnDelete();
            $table->string('descripcion');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_detalle');
        Schema::dropIfExists('compras');
    }
};
