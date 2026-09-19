<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->nullable();
            $table->decimal('precio_mensual', 10, 2)->default(0);
            $table->decimal('precio_anual', 10, 2)->default(0);
            $table->integer('limite_usuarios')->default(0); // 0 = ilimitado
            $table->integer('limite_ordenes')->default(0);  // 0 = ilimitado
            $table->text('descripcion')->nullable();
            $table->text('caracteristicas')->nullable(); // una por línea
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};
