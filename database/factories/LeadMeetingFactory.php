<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadMeeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadMeeting>
 */
class LeadMeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+2 weeks');
        $end = (clone $start)->modify('+1 hour');

        return [
            'lead_id' => Lead::factory(),
            'user_id' => User::factory(),
            'title' => fake()->randomElement(['Introductory Pitch Meeting', 'Sponsorship Discussion', 'Event Partnership Follow-up', 'Deal Closing Call']),
            'meeting_start' => $start,
            'meeting_end' => $end,
            'meeting_location_type' => fake()->randomElement(['online_meet', 'in_person', 'phone']),
            'meeting_link' => fake()->url(),
            'google_event_id' => null,
            'notes' => fake()->sentence(),
            'status' => 'scheduled',
        ];
    }
}
