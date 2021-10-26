<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreatingShopTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->json('POST','/api/shops');

        $response->assertStatus(201);
    }
    public function test()
    {
        $response = $this->json('GET','/api/products');

        $response->assertStatus(200);
    }
}
