<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use Illuminate\Support\Facades\Hash;
use App\Models\FavoriteProducts;

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
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error logging in customer',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function add_favorite_product(Request $request)
    {
        try {
            $user = auth()->user();
            $favorite_product = new FavoriteProducts([
                'customer_id' => $user->id,
                'product_id' => $request->product_id,
            ]);
            $favorite_product->save();
            return response()->json([
                'status' => 'success',
                'favorite_product' => $favorite_product,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error adding favorite product',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function remove_favorite_product(Request $request)
    {
        try {
            $user = auth()->user();
            $favorite_products = FavoriteProducts::where('customer_id', $user->id)->where('product_id', $request->product_id)->get();
            foreach ($favorite_products as $favorite_product) {
                $favorite_product->delete();
            }
            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error removing favorite product',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function get_favorite_products()
    {
        try {
            $user = auth()->user();
            $favorite_products = FavoriteProducts::where('customer_id', $user->id)->with('product')->get();
            return response()->json([
                'status' => 'success',
                'favorite_products' => $favorite_products,
                'products' => $favorite_products->map(function ($favorite_product) {
                    $product = $favorite_product->product;
                    $product->image;
                    return $product;
                }),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error getting favorite products',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
