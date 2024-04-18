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
            $table->unsignedBigInteger('player1_id')->nullable();
            $table->unsignedBigInteger('player2_id')->nullable();
            $table->unsignedBigInteger('winner_id')->nullable();
            $table->unsignedBigInteger('next_player_id')->nullable();
            $table->timestamps();
            
            $table->foreign('next_player_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('player1_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('player2_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('set null');
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
