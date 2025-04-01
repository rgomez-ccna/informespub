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
        Schema::create('registros', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_publicador')
                ->constrained('publicadors')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('a_servicio');
            $table->string('mes');

            $table->boolean('actividad')->nullable(); // <-- si participó o no (para publicadores simples)

            $table->integer('horas')->nullable();    // <-- solo visible si es auxiliar o precursor
            $table->integer('cursos')->nullable();
            $table->string('notas')->nullable();
            $table->string('aux')->nullable();       // <-- si fue auxiliar ese mes

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
        Schema::dropIfExists('registros');
    }
};
