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
class productTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
   /* public function test_get_all_products()
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->state([
            'user_id' => $user['id']
        ])->create();

        $product = Product::factory()->state([
            'shop_id' => $shop['id']
        ])->count(3)->create();

        print($product);

        $response = $this->json('get','api/products')
        ->assertStatus(200)
        ->assertJsonCount(count($product));
    }*/

     public function test_upload_product()
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->state([
            'user_id' => $user['id']
        ])->create();

        $token = $user->createToken('myapptoken')->plainTextToken;

        print($token);

        //action // performance
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('post', 'api/products',
            [
                'name' => 'Adidas shirt',
                'price' => 5.55,
                'stock' => 5,
                'shop_id' => $shop['id'],
                'category' => 'shirts'
            ]);
        $response
            ->assertStatus(201);
    }
}
