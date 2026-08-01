<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class IncomeExpenseChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string|Htmlable|null
    {
        return 'Income vs Expense (Current Year)';
    }

    protected function getData(): array
    {
        $months = collect(range(1, 12))->map(fn ($m) => Carbon::create(null, $m, 1)->format('M'));

        $incomeData = [];
        $expenseData = [];

        foreach (range(1, 12) as $month) {
            $incomeData[] = (float) Income::whereYear('income_date', now()->year)
                ->whereMonth('income_date', $month)
                ->sum('amount');

            $expenseData[] = (float) Expense::whereYear('expense_date', now()->year)
                ->whereMonth('expense_date', $month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Income (Rs.)',
                    'data' => $incomeData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
                [
                    'label' => 'Expense (Rs.)',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
