<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',30);
            $table->string('trip_date_time',20)->index();
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_voting')->default(false);
            $table->boolean('is_carpool')->default(false);
            $table->boolean('is_canceled')->default(false);
            $table->boolean('is_active')->default(false);
            $table->enum('transportation_method', ['car', 'boat','airplane','other'])->default('car');

            $table->integer('group_id')->unsigned();
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
        Schema::dropIfExists('trips');
    }
}
