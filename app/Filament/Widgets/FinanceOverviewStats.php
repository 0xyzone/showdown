<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Income;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceOverviewStats extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalIncome = (float) Income::sum('amount');
        $totalExpense = (float) Expense::sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        return [
            Stat::make('Total Income', 'Rs. '.number_format($totalIncome, 2))
                ->description('Total funds received')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Expenses', 'Rs. '.number_format($totalExpense, 2))
                ->description('Total expenditure')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Balance', 'Rs. '.number_format($netBalance, 2))
                ->description($netBalance >= 0 ? 'Surplus balance' : 'Deficit balance')
                ->descriptionIcon($netBalance >= 0 ? 'heroicon-m-banknotes' : 'heroicon-m-exclamation-triangle')
                ->color($netBalance >= 0 ? 'primary' : 'warning'),
        ];
    }
}
