<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, Product $product)
    {
        /*
         * Get all products
         */
        try {
            return response([
                'products' => Product::all()
            ]);
        }
        catch (\Throwable $e) {
            return response([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
     * Create a new product
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'price' => 'required|numeric',
                'discount_price' => 'nullable|numeric',
                'stock' => 'required|numeric',
                'category_id' => 'required|numeric',
                'image' => 'nullable|string',
            ]);
            $product = Product::create($data);
            return response([
                'product' => $product,
                'message' => 'Product created successfully'
            ], 201);
        }
        catch (\Throwable $e) {
            return response([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, Product $product)
    {
        try {
            return response([
                'product' => $product
            ]);
        }
        catch (\Throwable $e) {
            return response([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'price' => 'required|numeric',
                'discount_price' => 'nullable|numeric',
                'stock' => 'required|numeric',
                'category_id' => 'required|numeric',
                'image' => 'nullable|string',
            ]);
            $product->update($data);
            return response([
                'product' => $product,
                'message' => 'Product updated successfully'
            ], 200);
        }
        catch (\Throwable $e) {
            return response([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, Product $product)
    {
        try {
            $product->delete();
            return response([
                'message' => 'Product deleted successfully'
            ], 200);
        }
        catch (\Throwable $e) {
            return response([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
