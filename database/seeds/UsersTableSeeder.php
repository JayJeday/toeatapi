<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use App\User;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();
        
    	for ($i=0; $i < 4; $i++) { 
    		
    		User::create([
    	 	'name' => ('test' . $i),
    	 	'email' => ('test'.$i.'email.com'),
    	 	'password' => bcrypt('password')
       		]);	
    	}
      
    }
}
