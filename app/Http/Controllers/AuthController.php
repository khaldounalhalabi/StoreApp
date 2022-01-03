<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException ; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Exceptions\Handler ;
use Illuminate\Database\Eloquent\Model ;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens ; 
    public function register(Request $request)
   {
        $rules = 
        [
            'name'=>'max:55|min:3|string',
            'email'=>'email|required|unique:users',
            'password'=>['required' , 'confirmed' , Password::min(8)->letters()->mixedCase()->numbers()->symbols()] , 
            'profile_image_url' =>  ' string|nullable' , 
            'phone_number' => 'digits:10|required' , 
            'home_number' => 'digits:7|nullable' , 
            'facebook_url' => 'string|required' , 
            'birthdate' => 'date|before:-15 years'
        ];

        $validator = Validator::make($request->all() , $rules) ; 
        if($validator -> fails())
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$validator->errors()]) ; 
        }
        try
        {
            $RegisterData = $request->input() ; 
            $RegisterData['password'] = bcrypt($request->password);

            $user = new User ; 
            $user->name = $RegisterData['name'] ; 
            $user->email = $RegisterData['email'] ;
            $user->password = $RegisterData['password'] ; 
            $user->profile_image_url = $RegisterData['profile_image_url'] ; 
            $user->phone_number = $RegisterData['phone_number'] ; 
            $user->home_number = $RegisterData['home_number'] ; 
            $user->facebook_url = $RegisterData['facebook_url'] ; 
            $user->birthdate = $RegisterData['birthdate'] ; 
            
            $user->save() ;  

            $access_token = $user->createToken('authToken')->accessToken; ; 

            return response()->json(['message'=>'registered successfully','user'=> $user, 'access_token'=> $access_token]);
        }
        catch(\Exception $e)
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$e->getMessage()]) ; 
        }
   }


   public function login(Request $request)
   {
        $rules = 
        [
            'email'=>'email|required',
            'password'=>'required' 
        ];
       $validator = Validator::make($request->all() , $rules) ; 
       if($validator->fails())
       {
           return response()->json(['message'=>'there is been an error' , 'error message'=>$validator->errors()]) ;
       }
       $loginData = $request->input() ; 
        if(!auth()->attempt($loginData)) 
        {
            return response(['message'=>'Invalid credentials']);
        }
        
        $user = $request->user() ; 

        $data['user'] = $user ; 
        $data['access_token'] = $user->createToken('authToken')->accessToken;
        return response([$data , "message" => "data has been retrieved"]);

   }

   public function logout (Request $request) 
   {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['message'=>'you have been logged out']);
   }

   public function user_details()
   {
       $user = Auth::user() ; 
       return response()->json([$user]) ; 
   }
}
