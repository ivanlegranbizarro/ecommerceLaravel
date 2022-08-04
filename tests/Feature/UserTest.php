<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class UserTest extends TestCase
{
    public function authenticated()
    {
        Artisan::call('passport:install');
        $user = User::create([
            'name' => 'Antonio',
            'email' => time() . '@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'user'
        ]);

        if (!auth()->attempt(['email' => $user->email, 'password' => '123456'])) {
            $this->fail('User not authenticated');
        }
        return $user->createToken('Auth token')->accessToken;
    }

    /**
     * Test if the user can be retrieved
     */
    public function test_an_user_can_be_retrieved()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticated();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get(route('api.auth.me'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user'
        ]);
    }

    public function test_logout()
    /**
     * Test if the user can be logged out
     */
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticated();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post(route('api.auth.logout'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message'
        ]);
    }
}
