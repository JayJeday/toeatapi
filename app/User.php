<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','profile_pic'
    ];

     protected $uploads = '/images/';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','pivot'
    ];

    

    public function getFileAttribute($user){

        return $this->uploads . $user;
    }

    /**
     * The groups that belong to the user.
     */
    public function groups(){

            //generate update at and create at with timestamp
           return $this->belongsToMany('App\Group')->withPivot('is_admin')->withTimestamps();
    }

     /**
    users has many outings(trips,groups)
    */
    public function outings(){

      return  $this->hasMany('App\Outing');
    }

     public function getIsAdminAttribute($is_admin)
    {
         return (bool) $is_admin;
    }

     public function invites(){

      return  $this->hasMany('App\Invite');
    }
}
