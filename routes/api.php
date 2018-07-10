<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register','Api\Auth\RegisterController@register');
Route::post('login','Api\Auth\LoginController@login');
Route::post('refresh','Api\Auth\LoginController@refresh');

Route::middleware('auth:api')->group(function () {
	//dd("salut");
  //  return $request->user();
    Route::post('logout','Api\Auth\LoginController@logout');
    	
    //create group
    Route::post('user/group','Api\GroupController@store');

    //return all user groups
    Route::get('user/groups','Api\GroupController@index');

    //update group
    Route::put('user/group/{id}','Api\GroupController@update');

    //delete group
    Route::delete('user/group/{id}','Api\GroupController@destroy');

    //join group when invite is accepted
    Route::post('group/{id}/join','Api\GroupController@join');


    //********Trip************************
    //group trip
    Route::post('group/{id}/trip','Api\TripController@store');

    //update trip
	//Route::put('group/{id}/trip/{id}','Api\TripController@update');

    //get trip restaurant
    Route::get('group/{id}/trip', 'Api\TripController@show');

    //join trip
    Route::post('group/{id}/trip/{tripId}/join','Api\TripController@join');

    //unjoin trip
    Route::delete('group/{id}/trip/{tripId}/unjoin','Api\TripController@unjoin');

     //trip history response
     Route::get('user/triphistory','Api\TripController@tripHistories');

     Route::get('user/upcomingtrips','Api\TripController@upcomingTrips');

     //complete trip
      Route::put('trip/{id}/completed', 'Api\TripController@completeTrip');

    //******* Voting **********
    Route::put('group/{id}/trip/{tripId}/vote', 'Api\TripController@vote');

    Route::get('trip/{tripId}/results', 'Api\TripController@getVoteResult');

    //******Invite controller***********
    // send invite to user
     Route::post('invite/send','Api\InviteController@sendInvite');

     Route::delete('group/{groupId}/user/{userId}/invite/delete','Api\InviteController@deleteInvite');

     //get user invites
     Route::get('user/invites','Api\InviteController@getInvites');

     //************ Search conntroller ************
     Route::get('group/{id}/search/user','Api\SearchController@searchForUser');

     // searchview search
     Route::get('search/username','Api\SearchController@searchUserByUserName');

    

    
});
