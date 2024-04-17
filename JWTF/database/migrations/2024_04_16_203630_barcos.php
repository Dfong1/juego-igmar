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
        Schema::create('barcos', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->integer('user_barcos');
            $table->foreignId('rival_id')->references('id')->on('users');
            $table->integer('rival_barcos');

            $table->text('coordenate_user');
            $table->text('coordenate_rival');

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
        Schema::dropIfExists('barcos');
    }
};
