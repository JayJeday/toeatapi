<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
     protected $fillable = [
        'group_id', 'user_id','answered','group_name'];

        public function users(){
        	 return  $this->hasMany('App\User');
        }
}
