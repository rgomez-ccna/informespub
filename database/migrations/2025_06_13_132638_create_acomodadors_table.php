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
        Schema::create('acomodadors', function (Blueprint $table) {
            $table->id();

            $table->date('fecha'); // Ej: 2025-05-07

            $table->string('acceso_1');
            $table->string('acceso_2');
            $table->string('auditorio');

            $table->text('nota_final')->nullable(); // Texto inferior (se guarda en el último registro del mes)

            $table->boolean('es_nuevo_programa')->default(false);
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
        Schema::dropIfExists('acomodadors');
    }
};
