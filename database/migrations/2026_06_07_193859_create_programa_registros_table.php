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
        Schema::create('programa_registros', function (Blueprint $table) {
            $table->id();

            $table->foreignId('congregacion_id')
                ->constrained('congregacions')
                ->cascadeOnDelete();

            $table->foreignId('programa_id')
                ->constrained('programas')
                ->cascadeOnDelete();

            $table->foreignId('programa_bloque_id')
                ->nullable()
                ->constrained('programa_bloques')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('fecha')->nullable();

            $table->string('titulo')->nullable();
            $table->string('estado')->default('activo');

            $table->string('tipo_fila', 30)->default('normal');
            $table->text('texto_especial')->nullable();

            $table->integer('orden')->default(0);

            $table->timestamps();

            $table->index(['congregacion_id', 'programa_id', 'fecha']);
            $table->index('programa_bloque_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programa_registros');
    }
};
