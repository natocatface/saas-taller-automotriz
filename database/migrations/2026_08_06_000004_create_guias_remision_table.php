<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guias_remision', function (Blueprint $table) {
            $table->id();
            $table->string('serie', 10)->default('T001');
            $table->string('numero');
            $table->date('fecha');

            // Motivo y modalidad de traslado
            $table->string('motivo_codigo', 5)->default('01');   // catálogo 20
            $table->string('motivo_desc', 200)->nullable();
            $table->string('modalidad', 2)->default('02');       // 01 público, 02 privado
            $table->date('fecha_traslado');
            $table->decimal('peso_total', 10, 3)->default(0);
            $table->string('unidad_peso', 5)->default('KGM');
            $table->unsignedInteger('num_bultos')->nullable();

            // Punto de partida y llegada
            $table->string('partida_ubigeo', 10)->nullable();
            $table->string('partida_direccion', 250)->nullable();
            $table->string('llegada_ubigeo', 10)->nullable();
            $table->string('llegada_direccion', 250)->nullable();

            // Destinatario
            $table->string('destinatario_tipo_doc', 2)->default('6'); // catálogo 06
            $table->string('destinatario_num_doc', 20)->nullable();
            $table->string('destinatario_nombre', 200)->nullable();

            // Transporte público
            $table->string('transportista_doc', 20)->nullable();
            $table->string('transportista_nombre', 200)->nullable();
            $table->string('transportista_mtc', 30)->nullable();

            // Transporte privado
            $table->string('vehiculo_placa', 15)->nullable();
            $table->string('chofer_doc', 20)->nullable();
            $table->string('chofer_licencia', 30)->nullable();
            $table->string('chofer_nombre', 200)->nullable();

            // Bienes trasladados (JSON: [{descripcion, cantidad, unidad, codigo}])
            $table->json('items')->nullable();

            $table->foreignId('orden_id')->nullable()->constrained('ordenes')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Estado ante SUNAT (mismos nombres que en comprobantes para reutilizar el mapeo)
            $table->string('estado', 20)->default('emitido');    // emitido | anulado
            $table->string('estado_sunat', 20)->default('pendiente');
            $table->string('hash_cpe', 100)->nullable();
            $table->string('sunat_ticket', 60)->nullable();
            $table->string('xml_path', 250)->nullable();
            $table->string('cdr_path', 250)->nullable();
            $table->text('sunat_observaciones')->nullable();
            $table->timestamp('enviado_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guias_remision');
    }
};
