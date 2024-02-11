<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Travel;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(20),
            'travel_id' => Travel::first()->id,
            'starting_date' => fake()->dateTime(),
            'ending_date' => fake()->dateTime(),
            'price' => rand(1,100)
        ];
    }
}
