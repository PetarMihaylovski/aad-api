<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class ShopControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShow_shouldReturnShop_andStatusCode200()
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $rsp = $this->json('GET', "api/shops/{$shop->id}");

        $rsp
            ->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJson([
                "ownerId" => $user->id,
                "name" => $shop->name,
                "description" => $shop->description,
            ]);
    }

    public function testIndex_shouldReturnAllShops_andStatusCode200()
    {
        User::factory()->count(10)->create();
        $shops = Shop::factory()->count(2)->create();

        $rsp = $this->json('GET', "api/shops");

        $rsp->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonCount($shops->count());
    }

    public function testShow_shouldReturnError_andStatusCode404()
    {

        $id = 1;
        $rsp = $this->json('GET', "api/shops/{$id}");

        $rsp
            ->assertStatus(ResponseAlias::HTTP_NOT_FOUND)
            ->assertJson([
                "message" => "Shop with ID: {$id} not found!"
            ]);
    }

    public function testStore_shouldReturnErrorMessage_andStatusCode401()
    {

        // not defining a user

        $shop = [
            "name" => "Test Shop",
        ];

        $rsp = $this->json('POST', "api/shops", $shop);

        $rsp->assertStatus(ResponseAlias::HTTP_UNAUTHORIZED)
            ->assertJson([
                "message" => "Unauthenticated."
            ]);
    }

    public function testStore_shouldCreateShop_andStatusCode201()
    {
        $user = User::factory()->create();

        $shop = [
            "name" => "Test Shop",
            "description" => "Description for testing"
        ];

        $rsp = $this->actingAs($user)
            ->json('POST', "api/shops", $shop);

        $rsp->assertStatus(ResponseAlias::HTTP_CREATED)
            ->assertJson([
                "name" => $shop['name'],
                "ownerId" => $user['id'],
                "description" => $shop['description']
            ]);
    }

    public function testDelete_shouldReturnErrorMessage_andStatusCode403(){
        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $rsp = $this->actingAs($user)
            ->json('DELETE', "api/shops/{$shop['id']}");

        $rsp->assertStatus(ResponseAlias::HTTP_FORBIDDEN)
        ->assertJson([
            "message" => "Cannot delete a shop, that you do not own"
        ]);
    }
}
