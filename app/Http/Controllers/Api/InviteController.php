<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Invite;
use App\Group;
use App\User;

class InviteController extends Controller
{


    public function sendInvite(Request $request){

      $this->validate($request,[
    		      'groupId' => 'required',
    	       	'userId' => 'required'
        ]);

    $group = Group::find(request('groupId'));
    $user = User::find(request('userId'));

    if(!$group){
    	return response()->json(['message' => 'Group not found error'],401);
    }

    if(!$user){
    	return response()->json(['message' => 'User not found error'],401);
    }

    Invite::create(['group_id' => $group->id,'group_name' => $group->name, 'user_id'=> $user->id]);

    return response()->json(['message' => 'Invite was succesfully send'],200);

	}

    public function deleteInvite( $groupId,$userId){

    $group = Group::find($groupId);
    $user = User::find($userId);

    if(!$group){
        return response()->json(['message' => 'Group not found error'],401);
    }

    if(!$user){
        return response()->json(['message' => 'User not found error'],401);
    }

    Invite::where('group_id',$group->id)->where('user_id',$user->id)->delete();

    return response()->json(['message' => 'Invite was succesfully deleted'],200);

    }
    
	public function getInvites(){

		$user = Auth::user();

		$invites = Invite::where('user_id',$user->id)->where('answered',0)->get();

		 return response()->json(['invites' => $invites],200,[],JSON_NUMERIC_CHECK);
	}
}
