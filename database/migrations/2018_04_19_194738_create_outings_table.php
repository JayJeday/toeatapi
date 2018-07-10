<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->integer('trip_id')->unsigned();
            $table->integer('voting_place')->unsigned()->default(0);
            $table->boolean('voted')->default(false);
            $table->timestamps();
        });

        Schema::table('outings',function(Blueprint $table){

          $table->foreign('user_id')->references('id')->on('users');
          $table->foreign('group_id')->references('id')->on('groups');
          $table->foreign('trip_id')->references('id')->on('trips');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outings');
    }
}
