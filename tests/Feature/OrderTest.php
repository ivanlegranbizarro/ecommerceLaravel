<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A function for authenticate user.
     * @test
     */
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

    public function test_orders_can_be_retrieved()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $response = $this->get(route('api.orders.index'), [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            'orders'
        ]);
    }

    public function test_an_order_can_be_created()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $user = auth()->user();

        $category = Category::create([
            'name' => 'Category 1',
            'description' => 'Description 1'
        ]);

        $product1 = Product::create([
            'name' => 'Product 1',
            'description' => 'Description 1',
            'price' => '100',
            'category_id' => $category->id,
            'stock' => '10'
        ]);

        $product2 = Product::create([
            'name' => 'Product 2',
            'description' => 'Description 2',
            'price' => '200',
            'category_id' => $category->id,
            'stock' => '10'
        ]);


        Cart::create([
            'product_id' => $product1->id,
            'quantity' => '1',
            'user_id' => $user->id
        ]);

        Cart::create([
            'product_id' => $product2->id,
            'quantity' => '2',
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->json('POST', route('api.orders.store'), [
            'shipping_address' => 'Address 1',
            'order_address' => 'Address 2',
            'order_email' => 'order@order.com',
        ]);

        $order = Order::with('order_details')->first();

        $this->assertEquals($order->shipping_address, 'Address 1');
        $this->assertEquals($order->order_address, 'Address 2');
        $this->assertEquals($order->order_email, 'order@order.com');
        $this->assertEquals($order->order_status, 'pending');
        $this->assertEquals($order->total, '500');
        $this->assertCount(1, Order::all());


        $response->assertCreated();
    }
}
