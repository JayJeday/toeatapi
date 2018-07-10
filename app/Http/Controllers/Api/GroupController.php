<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Group;
use App\Outing;
use App\Trip;
use App\Invite;

//user group controller
class GroupController extends Controller{
    
	/*
	store user and set it has admin
	*/
    public function store(Request $request){
    	//get login user 
    	 $user = Auth::user();

    	 //validate client request
    	$this->validate($request,[
    		'name' => 'required',
    		'description' => 'required' 
    	]);

	  	$group = Group::create(['name' => request('name'),'description' => request('description')]);

	  	//add group to pivot table and make the user an admin
	  	$user->groups()->attach($group->id,['is_admin'=> true]);
 			
 		 return response()->json(['message' => 'Group succesfully created'],200);
    }

    /*
    get user all groups
    */
    public function index(){
    	//get loggin user instance
    	$groups = Auth::user()->groups()->get(['group_id','name','description','is_admin']);

        return response()->json(['data' => $groups],200,[],JSON_NUMERIC_CHECK);
    }

   /*
	update a Group attributes
	edit group description and admin power
   */
	public function update(Request $request, $id){

		//require change value in the request
		$this->validate($request,[
    		'admin_change' => 'required' 
    	]);

		
		//save the group content
		$user = Auth::user();
		$group = $user->groups->where('id',$id)->first();
		//$description = request('description');
		if($description = request('description')){
			$group->description = $description;
			$group->save();
			}
		
		//save user with admin power
		if(request('admin_change'))
		{
	  	 	$user->groups()->updateExistingPivot($id,['is_admin' => false]);

	  	 	if($newAdminId = request('newAdminId')){
	  	 		 $newAdmin = User::findOrFail($newAdminId)->groups()->updateExistingPivot($id, ['is_admin' => true]);
	  	 	}
		   			
		}

		return response()->json(['message' => 'Group has been succesfully updated'],200);
	}

	/*
		delete one or more groups
	*/
	public function destroy($id){
		//if user is admin delete the group with all the memebers
		$user = Auth::user();
		$is_admin = $user->groups->where('id','==',$id)->first()->pivot->is_admin;
		
		if($is_admin){
			//if user is admin delete the group and his members
			//delete outing by groups TODO refactor this

			Outing::where('group_id',$id)->delete();
			Group::find($id)->delete();
			Trip::where('group_id',$id)->delete();
			return response()->json(['message' => 'Group has been deleted by the Administrator'],200);
		}
		//delete outing by user id and group id
		Outing::where('group_id',$id)->where('user_id',$user->id)->delete();
		$user->groups()->detach($id);
		return response()->json(['message' => 'Successfull exit group']);
	}

	/*
		when user accept invitation
	*/
	public function join($id){

		$user = Auth::user();
		$group = Group::find($id);

		$invite = Invite::where('user_id',$user->id)->where('group_id',$id)->first();
		$invite->answered = 1;
		$invite->save();

		if(!$group){
			return response()->json(['message' => 'Group not found error'],401);
		}

		$user->groups()->attach($group->id);
		return response()->json(['message' => 'User succesfully joined'],200);
	}
}
