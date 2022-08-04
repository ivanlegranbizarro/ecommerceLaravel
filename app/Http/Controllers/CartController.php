<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $product = Product::findOrFail($request->product_id);
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->where('product_id', $product->id)->first();
            if ($cart) {
                $cart->quantity += $request->quantity;
                $cart->save();
            }
            else {
                $cart = new Cart();
                $cart->product_id = $product->id;
                $cart->user_id = $user->id;
                $cart->quantity = $request->quantity;
                $cart->save();
            }
            return response()->json(['success' => true, 'message' => 'Product added to cart successfully.'], 201);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Cart $cart)
    {
        try {
            return response()->json(['success' => true, 'cart' => $cart], 200);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $cart->quantity = $request->quantity;
            $cart->save();
            return response()->json(['success' => true, 'message' => 'Cart updated successfully.'], 200);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $cart->delete();
            return response()->json(['success' => true, 'message' => 'Cart deleted successfully.'], 200);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
