<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
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
    public function test_categories_can_be_retrieved()
    /*
     * Test that categories can be retrieved
     */
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('api.categories.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'categories'
        ]);
    }

    public function test_a_single_category_can_be_retrieved()
    /*
     * Test that a single category can be retrieved
     */
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Category Description'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get(route('api.categories.show', $category->id));


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'category'
        ]);
    }

    public function test_a_category_can_be_created()
    /*
     * Test that a category can be created
     */
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->post(route('api.categories.store'), [
            'name' => 'Test Category',
            'description' => 'Test Category Description'
        ]);

        $response->assertStatus(201);
        $this->assertEquals('Test Category', $response->json('category.name'));
        $this->assertEquals('Test Category Description', $response->json('category.description'));

        $response->assertJsonStructure([
            'category'
        ]);
    }

    public function test_a_category_can_be_updated()
    /*
     * Test that a category can be updated
     */
    {
        $this->withoutExceptionHandling();


        $token = $this->authenticated();

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Category Description'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->put(route('api.categories.update', $category->id), [
            'name' => 'Test Category Updated',
            'description' => 'Test Category Description Updated'
        ]);


        $updatedCategory = Category::find($category->id);

        $this->assertEquals($updatedCategory->name, 'Test Category Updated');
        $this->assertEquals($updatedCategory->description, 'Test Category Description Updated');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'category'
        ]);
    }

    public function test_a_category_can_be_deleted()
    /*
     * Test that a category can be deleted
     */
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticated();

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Category Description'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->delete(route('api.categories.destroy', $category->id));


        $this->assertDatabaseCount('categories', 0);

        $response->assertStatus(200);
    }
}
