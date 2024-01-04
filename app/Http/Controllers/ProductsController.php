<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\FavoriteProducts;

// use http

class ProductsController extends Controller
{
    public function index()
    {
        try {
            $products = Products::with('image')->get();

            return response()->json([
                'status' => 'success',
                'products' => $products,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error getting products',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function create(Request $request)
    {
        try {
            $product = new Products([
                'name' => $request->name,
                'details' => $request->details,
                'price' => $request->price ?? '0.00',
            ]);
            $product->save();
            if ($request->image)
                $product->update_image($request->image);
            return response()->json([
                'status' => 'success',
                'product' => $product,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating product',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function update(Request $request, Products $product)
    {
        try {
            // detect if price changed
            if ($request->price != $product->price) {
                // send notification to customers using curl request
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'included_segments' => [
                        'Subscribed Users',
                    ],
                    'contents' => [
                        'en' => $product->name . ' fiyatı değişti'
                    ]
                ]));
                // header
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Basic OTk0Mzg0ZWEtMDFiOC00NjgzLThhOWItOTVkOTFhMzc0ZWE5',
                    'content-type: application/json',
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec($ch);
                curl_close($ch);
            }
            $product->name = $request->name;
            $product->details = $request->details;
            $product->price = $request->price ?? '0.00';
            $product->save();
            if ($request->image)
                $product->update_image($request->image);
            return response()->json([
                'status' => 'success',
                'product' => $product,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating product',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function show(Products $product)
    {
        try {
            $product->load('image');
            // if user is signed in with middleware auth:customer
            // if (auth()->user())
            //     $product->is_favorite = FavoriteProducts::where('customer_id', auth()->user()->id)->where('product_id', $product->id)->exists();
            return response()->json([
                'status' => 'success',
                'product' => $product,
                'user' => auth()->user() ? auth()->user()->id : null,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error getting product',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function delete(Products $product)
    {
        try {
            $product->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting product',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
