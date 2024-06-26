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
     Schema::create('estadisticas', function (Blueprint $table){
      $table->id();
      $table->foreignId('user_id')->references('id')->on('users');
      $table->boolean('partida');
      $table->foreignId('rival_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        schema::dropIfExists('estadisticas');
    }
};
