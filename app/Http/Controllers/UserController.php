<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{

    public function register(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		"name" 		=> "required",
    		"email" 	=> "required|email",
    		"password" 	=> "required"
    	]);

	    if( $validator->fails() )
	    {
	    	return response()->json([
	    		"status" 			=> "failed",
	    		"validation_errors"	=> $validator->errors()
	    	]);
	    }

	    $inputs = $request->all();
	    $inputs["password"] = Hash::make($request->password);

	    try {

		    $user = User::create($inputs);

		    if( !is_null($user) )
		    {
		    	return response()->json([
		    		"status" 	=> "success",
		    		"message" 	=> "Registration Completed.",
		    		"data"	 	=> $user
		    	]);
		    } else {
		    	return response()->json([
		    		"status" 	=> "failed",
		    		"message" 	=> "Registration Failed.",
		    	]);
		    }
	    	
	    } catch (Exception $e) {

	    	return response()->json([
	    		"status" 	=> "error occurred.",
	    		"message" 	=> "Registration Failed.",
	    		"error" 	=> $e->getErrorMessage()
	    	]);
	    	
	    }
	}

	public function login(Request $request)
	{
		$validator = Validator::make($request->all(),[
			"email" 	=> "required|email",
			"password" 	=> "required"
		]);

	    if( $validator->fails() )
	    {
	    	return respond()->json([
	    		"status" 			=> "failed",
	    		"validation_errors"	=> $validator->errors()
	    	]);
	    }

		$user  = User::where("email", $request->email)->first();

        if( is_null($user) )
        {
            return response()->json([
            	"status" 	=> "failed", 
            	"message" 	=> "Failed! email not found"
            ]);
        }

        if( Auth::attempt(["email" => $request->email, "password" => $request->password]) )
        {
            $user  = Auth::user();
            $token = $user->createToken("token")->plainTextToken;

            return response()->json([
            	"status" => "success", 
            	"login"  => true, 
            	"token"  => $token, 
            	"data" 	 => $user
            ]);
        }
        else {

            return response()->json([
            	"status" 	=> "failed", 
            	"login" 	=> false, 
            	"message" 	=> "Wrong credentials provided."
            ]);
        }
	}

    public function user()
    {
        $user = Auth::user();

        if( !is_null($user) )
        { 
            return response()->json([
            	"status" => "success", 
            	"data" 	 => $user
            ]);

        } else {

            return response()->json([
            	"status" => "failed", 
            	"message" => "User not found."
            ]);
        }        
    }

    public function logout()
    {
        $user = Auth::user();

        if( !is_null($user) )
        {
        	$user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

            return response()->json([
            	"status"  => "success", 
            	"message" => "See you again."
            ]);

        } else {

            return response()->json([
            	"status" => "failed", 
            	"message" => "User not found."
            ]);
        }        
    }

}
