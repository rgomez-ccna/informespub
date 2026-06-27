<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vida_ministerio_asignacions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('congregacion_id')
                ->constrained('congregacions')
                ->cascadeOnDelete();

            $table->foreignId('vida_ministerio_id')
                ->constrained('vida_ministerios')
                ->cascadeOnDelete();

            $table->foreignId('vida_ministerio_parte_id')
                ->constrained('vida_ministerio_partes')
                ->cascadeOnDelete();

            $table->foreignId('publicador_id')
                ->constrained('publicadors')
                ->cascadeOnDelete();

            // Copia rápida para historial y recomendaciones
            $table->string('tipo_asignacion');

            // presidente, disertante, estudiante, ayudante, lector, conductor, oracion
            $table->string('rol')->default('principal');

            // general, principal, auxiliar
            $table->string('sala')->default('general');

            // Fecha del programa para consultar historial rápido
            $table->date('fecha');

            $table->unsignedSmallInteger('orden')->default(0);
            $table->text('notas')->nullable();

            $table->timestamps();

            $table->unique(
                ['vida_ministerio_parte_id', 'rol', 'sala'],
                'vm_asignacion_parte_rol_sala_unique'
            );

            $table->index(['congregacion_id', 'publicador_id', 'fecha'], 'vm_asignacion_publicador_fecha_index');
            $table->index(['congregacion_id', 'tipo_asignacion', 'fecha'], 'vm_asignacion_tipo_fecha_index');
            $table->index(['vida_ministerio_id', 'orden'], 'vm_asignacion_programa_orden_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vida_ministerio_asignacions');
    }
};