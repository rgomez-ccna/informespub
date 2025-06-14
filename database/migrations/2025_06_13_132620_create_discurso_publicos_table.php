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
        Schema::create('discurso_publicos', function (Blueprint $table) {
            $table->id();
    $table->date('fecha');
    $table->string('conferencia');
    $table->string('disertante');
    $table->string('congregacion');
    $table->string('horario')->nullable(); // solo para salidas
    $table->enum('tipo', ['visita', 'salida']); // define si es visita o salida
    $table->boolean('es_nuevo_programa_visita')->default(false);
    $table->boolean('es_nuevo_programa_salida')->default(false);
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
        Schema::dropIfExists('discurso_publicos');
    }
};
