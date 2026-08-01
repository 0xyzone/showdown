<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 20, 2000),
            'expense_date' => fake()->date(),
            'expense_type_id' => ExpenseType::factory(),
            'receipt_images' => [],
            'entered_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
