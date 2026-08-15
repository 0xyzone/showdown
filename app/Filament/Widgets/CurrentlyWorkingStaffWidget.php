<?php

namespace App\Filament\Widgets;

use App\Models\StaffAttendance;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CurrentlyWorkingStaffWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = -15;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Active Working Staff & Live Timesheets';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StaffAttendance::query()
                    ->with(['user'])
                    ->whereDate('date', Carbon::today())
                    ->whereNotNull('punch_in_at')
                    ->whereNull('punch_out_at')
                    ->latest('punch_in_at')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->weight('bold'),

                TextColumn::make('punch_in_at')
                    ->label('Clock In Time')
                    ->dateTime('h:i A')
                    ->fontFamily('mono'),

                TextColumn::make('formatted_worked_time')
                    ->label('Elapsed Duration')
                    ->badge()
                    ->color('success')
                    ->fontFamily('mono'),

                TextColumn::make('location_mode')
                    ->label('Location Mode')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'remote' ? 'primary' : 'gray')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('punch_in_distance_meters')
                    ->label('Distance from HQ')
                    ->suffix('m')
                    ->fontFamily('mono')
                    ->placeholder('—'),
            ])
            ->emptyStateHeading('No staff currently clocked in')
            ->emptyStateDescription('Active sessions will appear here as staff punch in.');
    }
}
