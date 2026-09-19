<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            // Estado del comprobante frente a SUNAT
            $table->string('estado_sunat', 20)->default('pendiente')->after('estado'); // pendiente | aceptado | rechazado | observado | anulado | error
            $table->string('hash_cpe', 100)->nullable()->after('estado_sunat');         // hash del XML firmado
            $table->string('sunat_ticket', 60)->nullable()->after('hash_cpe');           // ticket de baja/resumen
            $table->string('xml_path', 250)->nullable()->after('sunat_ticket');          // ruta del XML firmado
            $table->string('cdr_path', 250)->nullable()->after('xml_path');              // ruta del CDR de respuesta
            $table->text('sunat_observaciones')->nullable()->after('cdr_path');          // notas / observaciones SUNAT
            $table->timestamp('enviado_at')->nullable()->after('sunat_observaciones');   // fecha de envío
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->dropColumn([
                'estado_sunat', 'hash_cpe', 'sunat_ticket',
                'xml_path', 'cdr_path', 'sunat_observaciones', 'enviado_at',
            ]);
        });
    }
};
