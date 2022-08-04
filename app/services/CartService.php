<?php

namespace App\Services;
use App\Models\Cart;

class CartService
{
  public function getTotal()
  {
    $this->total = 0;
    $carts = Cart::join('products', 'products.id', 'carts.product_id')
      ->join('users', 'users.id', 'carts.user_id')
      ->where('users.id', auth()->user()->id)
      ->get();
    foreach ($carts as $cart) {
      $this->total += $cart->price * $cart->quantity;
    }
    return $this->total;
  }

  public function clear()
  {
    $carts = Cart::where('user_id', auth()->user()->id)->delete();
    return $carts;
  }
}