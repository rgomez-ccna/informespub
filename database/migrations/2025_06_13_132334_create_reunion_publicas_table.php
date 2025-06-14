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
       Schema::create('reunion_publicas', function (Blueprint $table) {
    $table->id();
    $table->date('fecha');
    $table->string('presidente');
    $table->string('lector');
    $table->boolean('es_nuevo_programa')->default(false); // <-- MARCA INICIO DE NUEVO BLOQUE
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
        Schema::dropIfExists('reunion_publicas');
    }
};
