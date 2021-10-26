<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetAllProducts extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->json('GET','/api/products');

        $response->assertStatus(200);
    }

    public function test()
    {
        $response = $this->json('GET','/api/products');

        $response->assertStatus(200);
    }
}
