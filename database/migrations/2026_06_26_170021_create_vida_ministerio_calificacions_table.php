<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vida_ministerio_calificacions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('congregacion_id')
                ->constrained('congregacions')
                ->cascadeOnDelete();

            $table->foreignId('publicador_id')
                ->constrained('publicadors')
                ->cascadeOnDelete();

            // presidente, tesoro, perlas, lectura_biblia, maestro_estudiante, etc.
            $table->string('tipo_asignacion');

            $table->boolean('activo')->default(true);
            $table->text('observacion')->nullable();

            $table->timestamps();

            $table->unique(['publicador_id', 'tipo_asignacion'], 'vm_calificacion_publicador_tipo_unique');
            $table->index(['congregacion_id', 'tipo_asignacion', 'activo'], 'vm_calificacion_filtro_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vida_ministerio_calificacions');
    }
};