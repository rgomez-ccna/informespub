<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vida_ministerios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('congregacion_id')
                ->constrained('congregacions')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('fecha');
            $table->time('hora_inicio')->nullable();

            $table->string('lectura_semanal')->nullable();
            $table->string('nombre_sala_auxiliar')->nullable();

            $table->string('cancion_inicio')->nullable();
            $table->string('cancion_medio')->nullable();
            $table->string('cancion_final')->nullable();

            $table->string('estado')->default('borrador');
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique(['congregacion_id', 'fecha']);
            $table->index(['congregacion_id', 'estado']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vida_ministerios');
    }
};