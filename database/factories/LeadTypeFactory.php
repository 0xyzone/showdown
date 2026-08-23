<?php

namespace Database\Factories;

use App\Models\LeadType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadType>
 */
class LeadTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Corporate Sponsor', 'Community Partner', 'Exhibitor', 'Media Partner', 'Vendor']),
        ];
    }
}
