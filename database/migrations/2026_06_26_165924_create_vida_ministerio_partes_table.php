<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vida_ministerio_partes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('congregacion_id')
                ->constrained('congregacions')
                ->cascadeOnDelete();

            $table->foreignId('vida_ministerio_id')
                ->constrained('vida_ministerios')
                ->cascadeOnDelete();

            // encabezado, tesoros, maestros, vida, final
            $table->string('seccion');

            // presidente, tesoro, perlas, lectura_biblia, maestro, vida_cristiana, estudio, oracion, etc.
            $table->string('tipo_asignacion');

            // Número visible: 1, 2, 3, 4...
            $table->unsignedTinyInteger('numero')->nullable();

            // Título visible de la parte
            $table->text('titulo')->nullable();

            // Duración de la parte
            $table->unsignedSmallInteger('duracion_minutos')->nullable();

            // Orden real dentro del programa
            $table->unsignedSmallInteger('orden')->default(0);

            // Horarios calculados o editables
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            // Referencias, modalidad, lección, notas importadas, etc.
            $table->json('metadata')->nullable();

            $table->timestamps();

             $table->unique(['vida_ministerio_id', 'orden'], 'vm_partes_programa_orden_unique');
            $table->index(['congregacion_id', 'tipo_asignacion']);
            $table->index(['vida_ministerio_id', 'seccion']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vida_ministerio_partes');
    }
};