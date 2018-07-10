<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table = 'places';

     
     protected $fillable = [
        'name', 'vicinity','latitude','longitude'];

     protected $hidden = ['created_at','updated_at'];

    /*
		this places belong to a specific trip
    */
    public function trip(){

    	return $this->belongTo('App\Trip');
    }


}
