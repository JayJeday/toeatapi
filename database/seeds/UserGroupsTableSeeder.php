<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Group;
use App\User;


class UserGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $counter = 0;
        for ($i=1; $i <= 2; $i++) {
        	//groups

        	$group = Group::find($i);

        	for ($x=1; $x <= 2; $x++) { 
                //2 for each group
        	 	++$counter;
			      User::find($counter)->groups()->attach($group->id,['is_admin' => ($x%4)==0]);
        	 } 
        }

    }
}
