<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "email" => $this->faker->email(),
            "username" => $this->faker->userName(),
            "password" => $this->faker->password(),
            "address" => $this->faker->address(),
            "postal" => $this->faker->postcode
        ];
    }
}
