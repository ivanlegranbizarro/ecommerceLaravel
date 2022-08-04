<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    /**
     *  Login a user and create an access token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $access_token = auth()->user()->createToken('authToken')->accessToken;
        return response([
            'user' => auth()->user(),
            'access_token' => $access_token
        ]);
    }

    public function register(Request $request)
    /**
     * Register a new user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:user,admin',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        $access_token = $user->createToken('authToken')->accessToken;
        return response([
            'user' => $user,
            'access_token' => $access_token
        ]);
    }
    public function me()
    /**
     * Get the authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    {
        return response([
            'user' => auth()->user()
        ]);
    }

    public function logout()
    /**
     * Logout the user
     * @return \Illuminate\Http\JsonResponse
     */
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response([
            'message' => 'Successfully logged out'
        ]);
    }
}
