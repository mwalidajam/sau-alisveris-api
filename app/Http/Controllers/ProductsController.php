<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;

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
            return response()->json([
                'status' => 'success',
                'product' => $product,
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
