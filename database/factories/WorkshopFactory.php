<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->catchPhrase(),
            'speaker' => $this->faker->name(),
            'location' => 'Room ' . $this->faker->numberBetween(100, 999),
            'total_seats' => $this->faker->numberBetween(5, 50),
        ];
    }
}
