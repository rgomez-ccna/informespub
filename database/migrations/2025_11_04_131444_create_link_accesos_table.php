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
        Schema::create('link_accesos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('congregacion_id')
                ->nullable()
                ->constrained('congregacions')
                ->cascadeOnDelete();

            $table->string('token', 80)->unique();
            $table->dateTime('expires_at')->index();
            $table->string('password_hash')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['congregacion_id', 'expires_at']);

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
        Schema::dropIfExists('link_accesos');
    }
};
