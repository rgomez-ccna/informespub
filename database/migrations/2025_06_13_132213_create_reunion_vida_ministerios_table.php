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
        Schema::create('reunion_vida_ministerios', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            
            // Encabezado
            $table->string('lectura_semanal')->nullable();
            $table->string('presidente')->nullable();
            $table->string('consejero_auxiliar')->nullable();
            $table->string('cancion_inicio')->nullable();
            $table->string('oracion_inicio')->nullable();

            // Tesoros de la Biblia
            $table->string('tesoro_titulo')->nullable();
            $table->string('tesoro_disertante')->nullable();
            $table->string('perlas_disertante')->nullable();
            $table->string('lectura_lector_principal')->nullable();
            $table->string('lectura_lector_auxiliar')->nullable();

            // Seamos Mejores Maestros (4 asignaciones, con título, estudiante y ayudante por sala)
            $table->json('asignaciones_maestros')->nullable();

            // Nuestra Vida Cristiana
            $table->string('cancion_medio')->nullable();
            $table->json('vida_cristiana')->nullable(); // 2 temas: título y disertante

            // Estudio de congregación
            $table->string('estudio_conductor')->nullable();
            $table->string('estudio_lector')->nullable();

            // Final
            $table->string('cancion_final')->nullable();
            $table->string('oracion_final')->nullable();

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
        Schema::dropIfExists('reunion_vida_ministerios');
    }
};
