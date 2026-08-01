<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_belongs_to_entered_by_user(): void
    {
        $user = User::factory()->create();

        $income = Income::factory()->create([
            'entered_by' => $user->id,
            'income_type' => 'sponsorship',
        ]);

        $this->assertEquals($user->id, $income->enteredBy->id);
        $this->assertDatabaseHas('incomes', [
            'id' => $income->id,
            'entered_by' => $user->id,
            'income_type' => 'sponsorship',
        ]);
    }

    public function test_expense_belongs_to_expense_type_and_user(): void
    {
        $user = User::factory()->create();
        $type = ExpenseType::factory()->create(['name' => 'Equipment']);

        $expense = Expense::factory()->create([
            'entered_by' => $user->id,
            'expense_type_id' => $type->id,
            'amount' => 1500.50,
        ]);

        $this->assertEquals($user->id, $expense->enteredBy->id);
        $this->assertEquals($type->id, $expense->expenseType->id);
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'entered_by' => $user->id,
            'expense_type_id' => $type->id,
            'amount' => 1500.50,
        ]);
    }
}
