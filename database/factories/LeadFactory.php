<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lead_type_id' => LeadType::factory(),
            'lead_status_id' => LeadStatus::factory(),
            'company_name' => fake()->company(),
            'contact_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'gmap_link' => fake()->url(),
            'notes' => fake()->sentence(),
        ];
    }
}
