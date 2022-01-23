<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class ShopControllerTest extends TestCase
{
    use RefreshDatabase;

//    public function testIndex_shouldReturnAllShops_andStatusCode200()
//    {
//        // arrange
//        $users = User::factory()->count(10)->create();
//        $shops = Shop::factory()->count(3)->create();
//
//        //act
//        $rsp = $this->json('GET','api/shops');
//
//        //assert
//        $rsp->assertStatus(ResponseAlias::HTTP_OK)
//            ->assertJsonStructure([
//                'data.*' => ['shopId', 'ownerId', 'name', 'description', 'imageName', 'imageURL'],
//            ]);
//    }

    public function testShow_shouldReturnShop_andStatusCode200(){
        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $rsp = $this->json('GET',"api/shops/{$shop->id}");

        $rsp
            ->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure([
               'data' => ['shopId', 'ownerId', 'name', 'description', 'imageName', 'imageURL'],
            ])
            ->assertJson([
                "ownerId" => $user->id,
                "name" => $shop->name,
                "description" => $shop->description,
            ]);
    }
}
