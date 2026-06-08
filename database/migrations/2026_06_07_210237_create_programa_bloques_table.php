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
        Schema::create('programa_bloques', function (Blueprint $table) {
            $table->id();

            $table->foreignId('congregacion_id')
                ->constrained('congregacions')
                ->cascadeOnDelete();

            $table->foreignId('programa_id')
                ->constrained('programas')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nombre');
            $table->text('descripcion')->nullable();

            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            $table->text('observaciones')->nullable();

            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);

            $table->timestamps();

            $table->index(['congregacion_id', 'programa_id']);
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programa_bloques');
    }
};
