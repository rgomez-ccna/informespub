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
        Schema::create('programa_campos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('programa_id')
                ->constrained('programas')
                ->cascadeOnDelete();

            $table->string('nombre');
            $table->string('slug');
            $table->string('tipo'); // texto, textarea, numero, fecha, hora, select, checkbox
            $table->json('opciones')->nullable();

            $table->boolean('obligatorio')->default(false);
            $table->boolean('visible_en_listado')->default(true);
            $table->boolean('buscable')->default(false);
            $table->boolean('activo')->default(true);

            $table->integer('orden')->default(0);

            $table->timestamps();

            $table->unique(['programa_id', 'slug']);
            $table->index(['programa_id', 'activo', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programa_campos');
    }
};
