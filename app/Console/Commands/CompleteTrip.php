<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompleteTrip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'complete:trips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set complete to one on trips that their dates is past';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //get all the trips that there dates pass today dates
        Trip::where('trip_date_time', '<=', $currentDate)->where('is_active',1)->update(['is_active' => 0,'is_completed' => 1]);

        Log::info("Trip completed");
    }
}
