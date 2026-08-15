<?php

namespace App\Filament\Resources\StaffPunchEvents\Tables;

use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StaffPunchEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('Timestamp')
                    ->dateTime('M d, Y • h:i:s A')
                    ->fontFamily('mono')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('event_type')
                    ->label('Event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'punch_in' => 'success',
                        'punch_out' => 'amber',
                        'credential_registered' => 'info',
                        'manual_correction' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'punch_in' => 'Punch In',
                        'punch_out' => 'Punch Out',
                        'credential_registered' => 'Device Registered',
                        'manual_correction' => 'Manual Correction',
                        default => ucfirst($state),
                    }),

                TextColumn::make('status')
                    ->label('Result')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'success' ? 'success' : 'danger'),

                TextColumn::make('failure_reason')
                    ->label('Failure Reason / Notes')
                    ->limit(30)
                    ->placeholder('—')
                    ->color('danger'),

                TextColumn::make('distance_meters')
                    ->label('Distance')
                    ->suffix('m')
                    ->fontFamily('mono')
                    ->placeholder('—'),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Staff Member')
                    ->options(User::pluck('name', 'id')),

                SelectFilter::make('event_type')
                    ->label('Event Type')
                    ->options([
                        'punch_in' => 'Punch In',
                        'punch_out' => 'Punch Out',
                        'manual_correction' => 'Manual Correction',
                    ]),

                SelectFilter::make('status')
                    ->label('Result')
                    ->options([
                        'success' => 'Success',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('occurred_at', 'desc');
    }
}
