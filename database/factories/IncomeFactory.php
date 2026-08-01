<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Income>
 */
class IncomeFactory extends Factory
{
    protected $model = Income::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 50, 5000),
            'income_date' => fake()->date(),
            'income_type' => fake()->randomElement(['sponsorship', 'donation', 'self_contribution']),
            'received_from' => fake()->company(),
            'received_by' => fake()->name(),
            'entered_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
