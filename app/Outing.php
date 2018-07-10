<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outing extends Model
{

	 protected $fillable = [
        'user_id','trip_id','group_id','voting_place','voted'
    ];

    protected $hidden = ['created_at','updated_at'];

    public function user(){
    	 return $this->belongsTo('App\User');
    }

    public function group(){
    	 return $this->belongsTo('App\Group');
    }

    public function trip(){
    	 return $this->belongsTo('App\Trip');
    }

     public function getVotedAttribute($voted)
    {
         return (bool) $voted;
    }

}
