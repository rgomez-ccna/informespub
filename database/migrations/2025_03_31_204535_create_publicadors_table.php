<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('publicadors', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->date('fnacimiento')->nullable();
            $table->date('fbautismo')->nullable();

            $table->boolean('hombre')->nullable();
            $table->boolean('mujer')->nullable();
            $table->boolean('oo')->nullable();
            $table->boolean('ungido')->nullable();
            $table->boolean('anciano')->nullable();
            $table->boolean('sv')->nullable();
            $table->boolean('precursor')->nullable();

            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('mail')->nullable();

            $table->string('grupo')->nullable();
            $table->string('rol')->nullable(); // ✅ NUEVO campo para cargo, función o nota interna
            $table->string('estado')->nullable(); // ya lo usabas en tus filtros (censurado, expulsado, etc.)

            $table->timestamps();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('publicadors');
    }
};
