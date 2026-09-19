<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_electronica', function (Blueprint $table) {
            $table->id();

            // Estado y modo
            $table->boolean('habilitado')->default(false);
            $table->boolean('emitir_automatico')->default(true);
            $table->string('driver', 30)->default('none');   // none | greenter
            $table->string('modo', 20)->default('beta');     // beta | produccion

            // Datos del emisor (aparecen en el comprobante electrónico)
            $table->string('ruc', 20)->nullable();
            $table->string('razon_social', 200)->nullable();
            $table->string('nombre_comercial', 200)->nullable();
            $table->string('direccion_fiscal', 250)->nullable();
            $table->string('ubigeo', 10)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();

            // Credenciales SUNAT (clave SOL y certificado)
            $table->string('usuario_sol', 60)->nullable();
            $table->text('clave_sol')->nullable();               // encriptada
            $table->string('certificado_path', 250)->nullable(); // ruta al .pem
            $table->text('certificado_password')->nullable();    // encriptada

            // GRE / API REST (opcional, para guías / nuevos servicios)
            $table->string('client_id', 120)->nullable();
            $table->text('client_secret')->nullable();           // encriptada

            $table->timestamps();
        });

        // Fila única inicial en modo beta con datos de homologación SUNAT
        DB::table('facturacion_electronica')->insert([
            'habilitado'        => false,
            'emitir_automatico' => true,
            'driver'            => 'none',
            'modo'              => 'beta',
            'ruc'               => '20000000001',
            'razon_social'      => 'EMPRESA DEMO S.A.C.',
            'nombre_comercial'  => 'AutoTaller Pro',
            'direccion_fiscal'  => 'Av. Principal 123',
            'ubigeo'            => '150101',
            'departamento'      => 'LIMA',
            'provincia'         => 'LIMA',
            'distrito'          => 'LIMA',
            'usuario_sol'       => 'MODDATOS',
            'clave_sol'         => null,
            'certificado_path'  => storage_path('facturacion/pe/certificate.pem'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_electronica');
    }
};
