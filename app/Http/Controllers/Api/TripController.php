<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Outing;
use App\Trip;
use App\Group;
use Carbon\Carbon;
use App\Place;

class TripController extends Controller
{

	/*
		Create trip 
	*/
    public function store(Request $request,$id){
    	
    	$user = Auth::user();
		$isAdmin = $user->groups->where('id','==',$id)->first()->pivot->is_admin;
    	
    	if(!$isAdmin){
			return response()->json(['message' => 'User does not have permission to create trips'],401);
    	}
    	
        $this->validate($request,[
    		      'title' => 'required',
    	       'trip_date_time' => 'required'
        ]);

        $group = Group::findOrFail($id);
        
        //search for active trips, if there is none active, activate this one 
        //todo later:: test with exist
        $is_active = false;

        if($activeGroup = $group->trips->where('is_active',1)->first())
        {
            //there is an active group already
            $is_active = false;
        }else
        {
            $is_active  = true;
        }
        

        //*******TODO:: format date before storing it in the server *************

        //insert trip
       $trip = $group->trips()->create(['title' => request('title'), 
       
        'trip_date_time' => request('trip_date_time'), 
        'is_voting' => request('is_voting'),'is_carpool' => request('is_carpool'), 'is_active' => $is_active]);


        //insert places, test how to insert multiples json objects in this case places
        //TODO find other way
        $array = $request->all();
        foreach ($array["places"] as $row) {
            $trip->places()->create(['name' => $row['name'],'vicinity' => $row['vicinity'],
         'latitude' => $row['latitude'], 'longitude' => $row['longitude']]);
        }
        

    	//create record of the trip, by default planner is going
    	Outing::create(['user_id' => $user->id,'group_id' => $id, 'trip_id' => $trip->id]);

    	return response()->json(['message' => 'Trip was succesfully created'],200);
    }

    public function update(Request $request,$groupId,$tripId){
    	//get user 
    	$user = Auth::user();
    	
    }

    /*
        show ative trip that is not cancelled and not completed
    */
    public function show($groupId){
        
        $group = Group::find($groupId);
        if(!$group){
            return response()->json(['message' => 'Group was not found'],401);
        }
        //get the active trip
        $trip = $group->trips->where('is_active',1)->first();

        if($trip){
        //get the user that are going to the trip    
        $outings = Outing::where('trip_id',$trip->id)->get();
        //append user to the trip
        foreach ($outings as $outing) {
             $outing->user;
         } 

         //todo manage admin unjoin

         //get all the json of the object
        return response()->json(['data' => ['trip' => Trip::with('places')->find($trip->id), 'outings' => $outings],'is_empty' => false],200,[],JSON_NUMERIC_CHECK);
        }
        else{
        //no there is no active trip
        return response()->json(['is_empty' => true],200);
       }
    }


/*
    update outings place votes for when the user submit the vote
*/
    public function vote(Request $request,$groupId,$tripId){
        //get current user
        $user = Auth::user();

        $outings = Outing::where('group_id',$groupId)->where('trip_id',$tripId)->where('user_id',$user->id)->first();

        $this->validate($request,[
              'voted_place_id' => 'required'
        ]);

        if(!$outings){
             return response()->json(['message' => 'Trip does not exist'],401);
        }

        $outings->voting_place = request('voted_place_id');
        $outings->voted = true;
        $outings->save();

        return response()->json(['message' => 'Vote succesfully submitted','voted' => true],200);
    }



   public function join($groupId,$tripId){

        $user = Auth::user();

        Outing::create(['user_id' => $user->id,'group_id' => $groupId, 'trip_id' => $tripId]);

         return response()->json(['message' => 'User succesfully joined'],200);
   } 

   public function unjoin($groupId,$tripId){
        $user = Auth::user();
       
        $results = Outing::where('user_id',$user->id)->where('group_id',$groupId)->where('trip_id', $tripId)->delete();
        
        if ($results) {
            
            return response()->json(['message' => 'User succesfully unjoined'],200);
        }

        return response()->json(['message' => 'User was not found'],404);
   }



   public function getVoteResult($tripId){

     //get the user that are going to the trip
    $outings = Outing::where('trip_id',$tripId)->get();

    //get votes from users
    $votes = $outings->pluck('voting_place');

     //Calculate votes
     $votesCalculated =  array_count_values($votes->toArray());

     //get the most voted place
     $voteCalcToCollection = collect($votesCalculated);

     $votesResult = $voteCalcToCollection->max();

     //filter the results to get the place or places with most votes
     $placeVoted = $voteCalcToCollection->filter(function($value,$key) use ($votesResult)
        { 
            return $value == $votesResult;
        });


     //get only the keys
     $placeId = $placeVoted->keys();
     //change vote status to false
     $trip = Trip::findOrFail($tripId)->first();
     $trip->is_voting = false;
     $trip->save();

     //TODO add results
     //if placeVoted size is 1 return the place id that was voted
     if ($placeId->count() == 1) {
        //delete all the places that are not the voted result
        Place::where('trip_id',$tripId)->where('id','!=',$placeId->first())->delete();
        return response()->json(['result' => $placeId->first(),'votes_results' => $voteCalcToCollection],200,[],JSON_NUMERIC_CHECK);
     }else{
        //pick the result randomly between the ties
        $randomPick = $placeId->random(1);
         Place::where('trip_id',$tripId)->where('id','!=',$randomPick->first())->delete();
            return response()->json(['result' => $randomPick->first(),'votes_results' => $voteCalcToCollection],200,[],JSON_NUMERIC_CHECK);
     }

   }

   public function completeTrip($id){
        $trip = Trip::find($id)->first();
        $trip->is_active = 0;
        $trip->is_completed = 1;
        $trip->save();

          return response()->json(['message' => 'Trip has been completed'],200,[],JSON_NUMERIC_CHECK);
   }

   public function  upcomingTrips(){
        $user = Auth::user();
        //get the user outing
        $outings = Outing::with('Trip')->where('user_id',$user->id)->get();
        
        if(!$outings->isEmpty()){
          
        //get the trips
        foreach ($outings as $outing) {
            $upcomingTrips = $outing->trip;
        }
        
        $trips = $upcomingTrips->withCount('outings')->where('is_completed',0)->where('is_canceled',0)->get()->sortBy('trip_date_time')->values()->all();

        return response()->json(['upcoming_trips' => $trips],200,[],JSON_NUMERIC_CHECK);

        }else
        {
            //empty array
            $list = collect([]);
             return response()->json(['upcoming_trips' => $list,'message' => 'No upcoming trips scheduled'],200);
         }
   }

   public function tripHistories(){

    $user = Auth::user();
    //get the user outings
    $outings = Outing::with('Trip')->where('user_id',$user->id)->get();

    if(!$outings->isEmpty()){
        //get the user trips
        foreach ($outings as $outing) {
            $trips = $outing->trip;
        }

        $completedTrips = $trips->with('places')->where('is_completed',1)->get();
        return response()->json(['completed_trips' => $completedTrips],200,[],JSON_NUMERIC_CHECK);
    }
        //empty array
    $list = collect([]);
        return response()->json(['completed_trips' => $list,'message' => 'no trip history'],200);
   }

    


}
