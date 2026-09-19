<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('empresa')->default('AutoTaller Pro');
            $table->string('ruc')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('moneda')->default('S/');
            $table->decimal('igv', 5, 2)->default(18);
            $table->string('serie_boleta')->default('B001');
            $table->string('serie_factura')->default('F001');
            $table->timestamps();
        });

        // Fila única inicial
        \Illuminate\Support\Facades\DB::table('configuraciones')->insert([
            'empresa' => 'AutoTaller Pro',
            'ruc' => '20601234567',
            'direccion' => 'Av. Principal 123, Lima',
            'telefono' => '01 456 7890',
            'email' => 'contacto@autotallerpro.com',
            'moneda' => 'S/',
            'igv' => 18,
            'serie_boleta' => 'B001',
            'serie_factura' => 'F001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};
