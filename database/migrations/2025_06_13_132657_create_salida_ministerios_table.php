<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salida_ministerios', function (Blueprint $table) {
           $table->id();

            $table->date('fecha'); // Ej: 2025-06-14
            $table->string('hora')->nullable(); // Ej: 10:45 o 19:00 (texto)
            $table->string('conductor')->nullable(); // Puede estar vacío en filas especiales
            $table->string('punto_encuentro')->nullable(); // Ej: Zoom o dirección
            $table->string('territorio')->nullable(); // Ej: 12 (Casas) o "A definir"

            $table->boolean('es_nueva_semana')->default(false); // Marca el inicio del cartel
            $table->boolean('es_fila_info')->default(false); // Marca fila de texto especial tipo "A definir por grupo"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salida_ministerios');
    }
};
