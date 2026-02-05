<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:8'
        ]);

        if(!Auth::guard('web')->attempt($request->only('email','password'))){
            return response()->json(['message'=>'Invalid Credentials'],401);
        }

        $user = Auth::guard('web')->user();

        return $this->generateTokenResponse($user);
    }

    public function register(Request $request){
        $credentials = $request->validate([
            'name'=>'string|required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
        ]);

        $user=User::create([
            'name'=>$credentials['name'],
            'email'=>$credentials['email'],
            'password'=>Hash::make($credentials['password'])
        ]);

        return $this->generateTokenResponse($user);
    }

    private function generateTokenResponse($user){
        //Access token : short lived(15 mins)
        $accessToken = $user->createToken('access_token', ['*'], now->addMinutes(15))->plainTextToken;
        
        //Refresh token: long lived (7 days)
        //This is stored in a secure cookie arey
        $refreshToken = $user->createToken('refresh_token', ['*'] ,now->addDays(7))->plainTextToken;

        return response()->json([
            'user'=>$user,
            'accessToken'=>$accessToken,
        ])->cookie('refresh_token',$refreshToken,10080,null,null,true,true);
        // cookie: name, value, mins, path, domain, secure, httpOnly: meaning refresh token is saved in http secured from the hackers from localStorage.
    }

    public function refresh(Request $request)
    {
        $tokenString = $request->cookie('refresh_token');
        if(!$tokenString) return response()->json(['message'=>'Unauthorized'],401);

        //Search and found the token in db? then->
        $token = PersonalAccessToken::findToken($tokenString);
        if(!$token || $token->expires_at->isPast()) return response()->json(['message'=>'Expired'],401);

        $user = $token->tokenable;
        $token->delele();

        return $this->generateTokenResponse($user);
    }

    // public function logout(Request $request){
    //     $user = $request->user();

    //     if(!$user){
    //         return response()->json([
    //         'message'=>'Not Authenticated'
    //     ],401);
    //     }
    //     $user->currentAccessToken()->delete();
    //     return response()->json([
    //         'message'=>'Logged out of this device'
    //     ],200);
        
    // }

    public function logout(Request $request)
    {
        $user = $request->user();

        if(!$user){
            return response()->json([
                'message'=>'Not Authenticated'
            ],401);
        }
        $user->tokens()->delete();
        return response()->json([
            'message'=>'Logged out'
        ],200)->withoutCookie('refresh_token');
    }
}
