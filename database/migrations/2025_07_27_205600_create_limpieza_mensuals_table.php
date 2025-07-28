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
        Schema::create('limpieza_mensuals', function (Blueprint $table) {
           $table->id();
            $table->date('fecha');
            $table->string('congregacion');
            $table->text('observaciones')->nullable();
            $table->text('observacion_general')->nullable();
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
        Schema::dropIfExists('limpieza_mensuals');
    }
};
