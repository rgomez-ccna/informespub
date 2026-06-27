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
        Schema::create('programa_valors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('programa_registro_id')
                ->constrained('programa_registros')
                ->cascadeOnDelete();

            $table->foreignId('programa_campo_id')
                ->constrained('programa_campos')
                ->cascadeOnDelete();

            $table->text('valor_texto')->nullable();
            $table->decimal('valor_numero', 12, 2)->nullable();
            $table->date('valor_fecha')->nullable();
            $table->time('valor_hora')->nullable();
            $table->json('valor_json')->nullable();

            $table->timestamps();

            $table->unique(['programa_registro_id', 'programa_campo_id'], 'programa_valores_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programa_valors');
    }
};
