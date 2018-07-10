<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Passport\Client;
use App\User;
use App\Photo;
use Illuminate\Support\Facades\Route;

class RegisterController extends Controller
{

    use IssueTokenTrait;
    
		private $client;

    	public function __construct(){
    		$this->client = Client::find(1);
    	}

  
    public function register(Request $request){
	
    	//registration validation
    	//validate client input 
    	$this->validate($request,[
    		'name' => 'required|unique:users',
    		'email' => 'required|email|unique:users,email', 
    		'password' => 'required|min:6'
    	]);

		//stored / inserted in user table
    	$user = User::create([
    		'name' => request('name'),
    		'email' => request('email'),
    	    'password' => bcrypt(request('password'))
    	]);

        //if request has a file
       if($profilePicture = $request->file('profile_pic')){

            //append file name with the current time and client
            $name = time() . $profilePicture->getClientOriginalName();

            //move file to the server with the appended name
             $profilePicture->move('uploads',$name);

            //save photo information in database
            $user->profile_pic = $name;
            $user->save();
       }

    	//parameters for requesting  the token
        $res =  $this->issueToken($request,'password');
        
         if ($res->status() == 401) {

              $response = json_decode($res->getContent(), true);    
              return response()->json(['message' => $response],401);
              
              }
        
        $response = json_decode($res->getContent(), true);
        return response()->json(['response' => $response,'user' => $user],200,[],JSON_NUMERIC_CHECK);
    }
}
