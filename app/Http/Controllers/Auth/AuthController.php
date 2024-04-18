<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'division_id' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ],[
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is registered',
            'division_id.required' => 'Division is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password min 6 characters',
            'password_confirmation.required' => 'Password confirmation is required',
            'password_confirmation.same' => 'Password confirmation not match'
        ]);

        User::create([
            'division_id' => $request->input('division_id'),
            'name' => ucwords($request->input('name')),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json(['message' => 'Register'], 200);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6',
        ],[
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.exists' => 'Email is not registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password min 6 characters'
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if(!Hash::check($request->input('password'), $user->password)) {
            return response()->json(['password' => ['Password is wrong']], 422);
        }

        // HANDLE JWT
        $payload = [
            'iat' => intval(microtime(true)),
            'exp' => intval(microtime(true)) + (60 * 60 * 1000),
            'uid' => $user->id
        ];
        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
        
        return response()->json([
            'message' => 'Login successfully', 
            'token' => $token,
            'user' => $user
        ]);
    }
}
