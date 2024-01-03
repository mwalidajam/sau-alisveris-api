<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAuth extends Controller
{
    // make login function
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (!auth()->attempt($data)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
        // create access token to use in api bearer token
        $accessToken = auth()->user()->createToken('auth_token')->accessToken;
        return response()->json([
            'token' => $accessToken,
            'user' => auth()->user()
        ], 201);
    }

    // make logout function
    public function logout()
    {
        // get the token that is used to authenticate the request
        $token = auth()->user()->token();
        // revoke the token
        $token->revoke();
        return response()->json([
            'message' => 'User logged out successfully'
        ], 200);
    }
}
