<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login()
    /**
     * It creates a user, logs in, and checks if the response contains an access token
     */
    {
        Artisan::call('passport:install');

        $this->withoutExceptionHandling();

        $user = User::create([
            'name' => 'Antonio',
            'email' => time() . '@gmail.com',
            'password' => bcrypt('123456')
        ]);
        $user->createToken('Auth token')->accessToken;

        $response = $this->post(route('api.login'), [
            'email' => time() . '@gmail.com',
            'password' => '123456'
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            'user',
            'access_token'
        ]);
    }

    public function test_register()
    /**
     * It creates a user, logs in, and checks if the response contains an access token
     */
    {
        Artisan::call('passport:install');

        $this->withoutExceptionHandling();

        $response = $this->post(route('api.register'), [
            'name' => 'Antonio',
            'role' => 'user',
            'email' => time() . '@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            'user',
            'access_token'
        ]);
    }
}
