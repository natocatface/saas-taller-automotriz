<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Amplía el tipo para admitir notas de crédito y débito
        DB::statement("ALTER TABLE comprobantes MODIFY COLUMN tipo VARCHAR(20) NOT NULL DEFAULT 'boleta'");

        Schema::table('comprobantes', function (Blueprint $table) {
            $table->foreignId('doc_afectado_id')->nullable()->after('orden_id')
                ->constrained('comprobantes')->nullOnDelete();       // comprobante que modifica la nota
            $table->string('motivo_codigo', 5)->nullable()->after('doc_afectado_id'); // catálogo 09 (NC) / 10 (ND)
            $table->string('motivo_desc', 200)->nullable()->after('motivo_codigo');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('doc_afectado_id');
            $table->dropColumn(['motivo_codigo', 'motivo_desc']);
        });

        DB::statement("ALTER TABLE comprobantes MODIFY COLUMN tipo ENUM('boleta','factura') NOT NULL DEFAULT 'boleta'");
    }
};
