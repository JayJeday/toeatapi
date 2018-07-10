<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;


class LoginController extends Controller
{

    use IssueTokenTrait;


	private $client;

    public function __construct(){
    	$this->client = Client::find(1);
    }

	public function login(Request $request){

		//  $this->validate($request,[
		// 	'email'=>'required',
		// 	'password'=>'required'
		// ]);

		 $validator = Validator::make($request->all(), [
   		 'email' => 'required',
   		 'password' => 'required'
		]);

		//return this if validator fail
		
		$user = User::where('email',request('email'))->first();

			  	$res =  $this->issueToken($request,'password');

//				manage code if is a 401 error
			  if ($res->status() == 401) {

			  $response = json_decode($res->getContent(), true);	
			  return response()->json(['message' => $response],401);
			  
			  }

       		 $response = json_decode($res->getContent(), true);

        	return response()->json(['response' => $response,'user' => $user],200,[],JSON_NUMERIC_CHECK);

			
	}

	public function refresh(Request $request){

//validate body
		$this->validate($request,[
			'refresh_token' => 'required'
		]);

		return	$this->IssueToken($request,'refresh_token');

	} 



	public function logout(Request $request){

		//get current user token
        $accessToken = Auth::user()->token();
        //get the refresh token of the access token to revoke it
        $refresh_token = DB::table('oauth_refresh_tokens')
        ->where('access_token_id',$accessToken->id)
        -> update(['revoked' => true]);

        $accessToken->revoke(); 

        return response()->json(['message' => 'User succesfully logout'],200);
	}

}
