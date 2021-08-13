<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\UserResource;
use Auth;

class UserController extends Controller
{
    public function register(Request $request){
        //Validate Request
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',

        ]);
       //Create User 
       $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
       ]);

       if(!$user){
           return response()->json([
               'error' => 'Unable to Register, Please try again!'
           ]);
       }
       //Create User Token 
       $token = $user->createToken($user->name)->accessToken;

       return response()->json([
           'data' => new UserResource($user),
           'token' => $token,
           'message' => 'Registration was successful'
       ], Response::HTTP_CREATED);
    }


    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',

        ]);
        
        //Check User Credentials
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            $token = Auth::user()->createToken(Auth::user()->name)->accessToken;

            return response()->json([
                'user' => new UserResource(Auth::user()),
                'token' => $token,
            ], Response::HTTP_ACCEPTED);
        }
        else{
            return response()->json([
                'error' => 'Invalid Login Details'
            ]);
        }
    }


    public function updateAccount(Request $request){
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required'
        ]);

        $user = Auth::user();

        //Update User
        $user->update($data);

        return response()->json([
            'user' => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    public function changePassword(Request $request){
        $data = $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = Auth::user();

        //Check if old password matches
        if(!Hash::check($data['old_password'], $user->password)){
            return response()->json([
                'error' => 'Old password does not match',
            ]);
        }

        //Update User Password
        $user->fill(['password' => Hash::make($data['password'])])->save();

        return response()->json([
            'success' => 'password changed successfully'
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request){
        
        Auth::user()->token()->delete();

        return response()->json([
            'success' => 'You logged out!'
        ]);
    }
}
