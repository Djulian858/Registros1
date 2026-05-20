<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->string('colaborador');
            $table->string('correo_colaborador')->nullable();
            $table->string('serial_pc');
            $table->string('tipo_equipo');
            $table->string('modelo_equipo')->nullable();
            $table->date('fecha_entrega');
            $table->string('hora_entrega')->nullable();
            $table->string('entregado_por')->nullable();
            $table->text('observaciones_prestamo')->nullable();
            $table->date('fecha_devolucion')->nullable();
            $table->string('hora_devolucion')->nullable();
            $table->text('observaciones_devolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
