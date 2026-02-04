<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:8'
        ]);

        $user = User::where('email',$credentials['email'])->first();

        if( !$user || Hash::check($credentials['password'], $user->password) ){
            return response()->json(['message'=>"Invalid Credentials"]);
        }

        $suer->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function register(Request $request){
        $credentials = $request->validate([
            'name'=>'string|required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&]).+$/'
        ]);

        $user=User::create([
            'name'=>$credentials['name'],
            'email'=>$credentials['email'],
            'password'=>Hash::make($credentials['password'])
        ]);

        $token=$user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
    }
}
