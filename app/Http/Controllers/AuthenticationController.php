<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();

        if(! $user || ! Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email'=> ['The provided credential are incorrect.'],
            ]);
        }
        
        $token =  $user->createToken('user login')->plainTextToken;
        return response()->json([
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email:dns|unique:users',
            'username' => 'required|min:3|max:255|unique:users',
            'password' => 'required|min:8|max:255',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
        ]);

        $user = User::create([
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
        ]);

        return $user;
    }

    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }

    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }
}
