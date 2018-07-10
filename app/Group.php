<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';

     protected $fillable = [
       'name', 'description',
    ];

protected $hidden = [
        'pivot'
    ];

     /**
     * The users that belong to the group.
     */

    public function users(){

    	return $this->belongsToMany('App\User');
    }

    /**
    groups has many outings(trips,users)
    get all the user going to the trip and other stuff
    */
    public function outings(){
   
         return	$this->hasMany('App\Outing');
    }

  
     /**
    get the trips for the group
    ex -> a trip that is active
    ex -> a trip that are cancelled
    ex -> history trips are completed
    */

    public function trips(){
        return $this->hasMany('App\Trip');
    }

     public function getIsAdminAttribute($is_admin)
    {
         return (bool) $is_admin;
    }

}
