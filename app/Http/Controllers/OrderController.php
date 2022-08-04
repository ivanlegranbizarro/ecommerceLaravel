<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\CartService;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * if user has role 'admin' then show all orders
     * if user has role 'user' then show only his orders
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Order $order)
    {
        try {
            $user = auth()->user();
            if ($user->role == 'admin') {
                $orders = $order->all();
            }
            else {
                $orders = $order->where('user_id', $user->id)->get();
            }
            return response()->json([
                'orders' => $orders
            ], 200);
        }
        catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Order $order)
    {
        try {
            $user = auth()->user();
            $cart = new CartService();
            $total = $cart->getTotal();
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'shipping_address' => $request->shipping_address,
                'order_address' => $request->order_address,
                'order_email' => $request->order_email,
                'order_status' => 'pending'
            ]);

            if ($order) {
                $cart->clear();
                return response()->json([
                    'message' => 'Order created successfully'
                ], 201);
            }
            else {
                return response()->json([
                    'message' => 'Order not created'
                ], 500);
            }
        }
        catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
