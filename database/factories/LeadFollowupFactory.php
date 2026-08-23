<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadFollowup>
 */
class LeadFollowupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'user_id' => User::factory(),
            'followup_date' => fake()->date(),
            'remarks' => fake()->paragraph(),
        ];
    }
}
