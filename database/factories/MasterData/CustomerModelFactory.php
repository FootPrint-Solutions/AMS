<?php

namespace Database\Factories\MasterData;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'contact' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
        ];
    }
}
