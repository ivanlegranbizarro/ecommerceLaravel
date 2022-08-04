<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
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


    /**
     * Test that a single product can be retrieved
     *
     * @return void
     */
    public function test_products_can_be_retrieved()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('api.products.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'products'
        ]);
    }

    public function test_product_can_be_created()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $category = Category::create([
            'name' => 'Category 1',
            'description' => 'Description 1'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->json('POST', route('api.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'price' => '10.00',
            'category_id' => $category->id,
        ]);

        $product = Product::first();

        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('Test Product Description', $product->description);
        $this->assertEquals('10.00', $product->price);
        $this->assertEquals($category->id, $product->category_id);

        $response->assertStatus(201);
    }

    public function test_product_can_be_retrieved()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'price' => '10.00',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->json('GET', route('api.products.show', $product->id));

        $response->assertStatus(200);
    }

    public function test_product_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'price' => '10.00',
            'category_id' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->json('PUT', route('api.products.update', $product->id), [
            'name' => 'Test Product Updated',
            'description' => 'Test Product Description Updated',
            'price' => '10.00',
            'category_id' => $product->category_id,
        ]);

        $product = Product::first();

        $this->assertEquals('Test Product Updated', $product->name);
        $this->assertEquals('Test Product Description Updated', $product->description);
        $this->assertEquals('10.00', $product->price);

        $response->assertStatus(200);
    }

    public function test_product_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'price' => '10.00',
            'category_id' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->json('DELETE', route('api.products.destroy', $product->id));

        $response->assertStatus(200);
        $this->assertCount(0, Product::all());
    }
}
