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
        Schema::create('asistencias', function (Blueprint $table) {
        $table->id();
        $table->integer('a_servicio'); // año servicio
        $table->string('mes');
        $table->enum('tipo',['FS','ES']); // FIN DE SEMANA / ENTRE SEMANA
        $table->integer('reuniones')->default(0);
        $table->integer('total')->default(0);
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
        Schema::dropIfExists('asistencias');
    }
};
