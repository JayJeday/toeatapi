<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Trip;


class TripsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i=0; $i < 10; $i++) {
        	Trip::create([
        		'title' => 'Trip'. $faker->numberBetween(1,100),
                'trip_date_time' => $faker->dateTime($max = 'now'), // '1979-06-09'
                'is_completed' => $faker->boolean($chanceOfGettingTrue = 60),
        		'is_voting' => $faker->boolean($chanceOfGettingTrue = 70),
        	    'is_carpool' => $faker->boolean($chanceOfGettingTrue = 50),
                'is_canceled' => $faker->boolean($chanceOfGettingTrue = 40),
                'group_id' => $faker->numberBetween(1,2)
            ]); 
            //simulate entry of each desk
            sleep(10);
        }
    }
}
