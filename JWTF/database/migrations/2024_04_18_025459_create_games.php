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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pendiente', 'activo', 'terminado'])->default('pendiente');
            $table->unsignedBigInteger('ganador_id')->nullable();
            $table->unsignedBigInteger('jugador_id')->nullable();

            $table->foreign('ganador_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('jugador_id')->references('id')->on('users')->onDelete('set null');

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
        Schema::dropIfExists('games');
    }
};
