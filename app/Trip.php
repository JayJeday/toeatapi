<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'trips';

   //     Todo format date
  //  protected $dates = ['trip_date_time'];

     protected $fillable = [
        'title','trip_date_time','is_voting','is_carpool','is_canceled','is_completed', 'is_active'
    ];


     /**
    trips has many outings(groups,users)
    to get information about user going to the trips
    */
    public function outings(){
    	return $this->hasMany('App\Outing');
    }


    /**
    get the places for the trip
    */

    public function places(){
        return $this->hasMany('App\Place');
    }

    /*
        this trip belong to a specific group
    */

    public function group(){

        return $this->belongTo('App\Group');
        
    }

    public function getIsVotingAttribute($is_voting)
    {
         return (bool) $is_voting;
    }

     public function getIsCarpoolAttribute($is_carpool)
    {
         return (bool) $is_carpool;
    }

     public function getIsCompletedAttribute($is_completed)
    {
         return (bool) $is_completed;
    }

     public function getIsActiveAttribute($is_active)
    {
         return (bool) $is_active;
    }

     public function getIsCanceledAttribute($is_canceled)
    {
         return (bool) $is_canceled;
    }
}
