<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use Illuminate\Support\Facades\Hash;

class CustomersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:customer', ['except' => ['register', 'login']]);
    }

    public function register(Request $request)
    {
        try {
            $customer = new Customers([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request['password']),
            ]);
            $customer->save();
            return response()->json([
                'status' => 'success',
                'customer' => $customer,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error registering customer',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $data = $request->only(['email', 'password']);
            $user = Customers::where('email', $data['email'])->firstOrFail();
            if (!$user || !Hash::check($data['password'], $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials',
                ], 401);
            }
            $token = $user->createToken('customer')->accessToken;
            return response()->json([
                'status' => 'success',
                'token' => $token,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error logging in customer',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
