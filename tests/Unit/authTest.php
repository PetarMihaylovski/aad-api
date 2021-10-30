<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Faker\Extension\Helper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\HelperMethods;
use Tests\TestCase;
class authTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_login()
    {
        $user = User::factory()->create(['password' => bcrypt('foo')]);

        $this->json('post','api/login', [
            'email' => $user['email'],
            'password' => 'foo'
        ])->assertStatus(201);
    }

    public function test_register()
    {
        $response =  $this->json('post','api/register', [
            'username' => 'genoompjes',
            'email' => 'niektempert@hotmail.com',
            'password' => 'test1234@',
            'address' => 'teststraat 5',
            'postal' => '5454gd'
        ]);

        $response->assertStatus(201);
    }

    public function test_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response =  $this->withHeader('Authorization', 'Bearer '.$token)->json('get','api/logout');

        $response->assertStatus(200);
    }
}
