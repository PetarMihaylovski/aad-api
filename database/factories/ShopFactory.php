<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ShopFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shop::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userIds = DB::table('users')->pluck('id')->all();
        return [
            'user_id' => $this->faker->randomElement($userIds),
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'image_name' => '',
            'image_url' => ''
        ];
    }
}
