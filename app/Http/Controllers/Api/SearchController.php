<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Invite;

class SearchController extends Controller
{
    public function searchForUser(Request $request,$id){

    	$usernameParam = $request->username;

        $data = User::whereDoesntHave('groups', function ($q) use ($id) { $q->where('group_id',$id);})
        ->where('name','like','%'.$usernameParam.'%')->get();

        //get all the user that are invited
        $invites = Invite::where('group_id',$id)->get();

    	return response()->json(['users' => $data,'invites' => $invites],200,[],JSON_NUMERIC_CHECK);
    }

// automatically search
    public function searchUserByUserName(Request $request){

    	$userSearchParam = $request->search;

    	$data = User::where('name','like','%'.$userSearchParam.'%')->get(['name']);

    	return response()->json(['names' => $data],200,[],JSON_NUMERIC_CHECK);

    }
}
