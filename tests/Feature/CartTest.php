<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
  use RefreshDatabase;
  public function authenticated()
  {
    Artisan::call('passport:install');
    $user = User::create([
      'name' => 'Antonio',
      'email' => time() . '@gmail.com',
      'password' => bcrypt('123456'),
      'role' => 'admin'
    ]);

    if (!auth()->attempt(['email' => $user->email, 'password' => '123456'])) {
      $this->fail('User not authenticated');
    }
    return $user->createToken('Auth token')->accessToken;
  }

  public function test_a_cart_can_be_created()
  {
    $this->withoutExceptionHandling();

    $token = $this->authenticated();

    $category = Category::create([
      'name' => 'Test Category',
      'description' => 'Test Description'
    ]);

    $product = $category->products()->create([
      'name' => 'Test Product',
      'description' => 'Test Description',
      'price' => '10.00',
      'category_id' => $category->id
    ]);

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
      'Accept' => 'application/json'
    ])->post(route('api.cart.store'), [
      'product_id' => $product->id,
      'quantity' => 5
    ]);

    $cart = Cart::first();

    $this->assertCount(1, Cart::all());
    $this->assertEquals($cart->product_id, $product->id);
    $this->assertEquals($cart->quantity, 5);

    $response->assertStatus(201);
  }

  public function test_a_cart_can_be_updated()
  {
    $this->withoutExceptionHandling();

    $token = $this->authenticated();

    $category = Category::create([
      'name' => 'Test Category',
      'description' => 'Test Description'
    ]);

    $product = $category->products()->create([
      'name' => 'Test Product',
      'description' => 'Test Description',
      'price' => '10.00',
      'category_id' => $category->id
    ]);

    $cart = Cart::create([
      'product_id' => $product->id,
      'quantity' => 5,
      'user_id' => 1
    ]);

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
      'Accept' => 'application/json'
    ])->json('PUT', route('api.cart.update', $cart->id), [
      'quantity' => 10
    ]);

    $cart = Cart::first();

    $this->assertEquals($cart->quantity, 10);

    $response->assertStatus(200);
  }

  public function test_a_cart_can_be_destroyed()
  {
    $this->withoutExceptionHandling();

    $token = $this->authenticated();

    $category = Category::create([
      'name' => 'Test Category',
      'description' => 'Test Description'
    ]);

    $product = $category->products()->create([
      'name' => 'Test Product',
      'description' => 'Test Description',
      'price' => '10.00',
      'category_id' => $category->id
    ]);

    $cart = Cart::create([
      'product_id' => $product->id,
      'quantity' => 5,
      'user_id' => 1
    ]);

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
      'Accept' => 'application/json'
    ])->json('DELETE', route('api.cart.destroy', $cart->id));

    $this->assertCount(0, Cart::all());

    $response->assertStatus(200);
  }

  public function test_a_cart_can_be_retrieved()
  {
    $this->withoutExceptionHandling();

    $token = $this->authenticated();

    $category = Category::create([
      'name' => 'Test Category',
      'description' => 'Test Description'
    ]);

    $product = $category->products()->create([
      'name' => 'Test Product',
      'description' => 'Test Description',
      'price' => '10.00',
      'category_id' => $category->id
    ]);

    $cart = Cart::create([
      'product_id' => $product->id,
      'quantity' => 5,
      'user_id' => 1
    ]);

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
      'Accept' => 'application/json'
    ])->json('GET', route('api.cart.show', $cart->id));

    $this->assertEquals($cart->quantity, 5);
    $this->assertEquals($cart->product_id, $product->id);
    $this->assertEquals($cart->user_id, 1);

    $response->assertJsonStructure([
      'cart'
    ]);
    $response->assertStatus(200);
  }
}
