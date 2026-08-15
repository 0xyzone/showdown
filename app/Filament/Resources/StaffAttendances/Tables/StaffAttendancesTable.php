<?php

namespace App\Filament\Resources\StaffAttendances\Tables;

use App\Models\StaffAttendance;
use App\Models\User;
use App\Services\StaffAttendanceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffAttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                if ($user && ! $user->hasRole('super_admin') && ! $user->hasRole('attendance_manager') && ! $user->can('ViewAny:StaffAttendance')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y (D)')
                    ->sortable(),

                TextColumn::make('punch_in_at')
                    ->label('Clock In')
                    ->dateTime('h:i A')
                    ->fontFamily('mono')
                    ->placeholder('—'),

                TextColumn::make('punch_out_at')
                    ->label('Clock Out')
                    ->dateTime('h:i A')
                    ->fontFamily('mono')
                    ->placeholder('—'),

                TextColumn::make('formatted_worked_time')
                    ->label('Worked Time')
                    ->fontFamily('mono')
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'working' => 'info',
                        'remote' => 'primary',
                        'half_day' => 'warning',
                        'manually_corrected' => 'amber',
                        default => 'gray',
                    }),

                TextColumn::make('location_mode')
                    ->label('Mode')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'remote' ? 'info' : 'gray'),

                IconColumn::make('punch_in_verified_biometric')
                    ->label('Biometric')
                    ->boolean()
                    ->trueIcon('heroicon-o-finger-print')
                    ->falseIcon('heroicon-o-map-pin')
                    ->trueColor('success')
                    ->falseColor('gray'),

                IconColumn::make('is_manually_corrected')
                    ->label('Corrected')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Staff Member')
                    ->options(User::pluck('name', 'id'))
                    ->visible(fn (): bool => auth()->user()?->hasRole('super_admin') || auth()->user()?->hasRole('attendance_manager')),

                SelectFilter::make('status')
                    ->label('Attendance Status')
                    ->options([
                        'working' => 'Currently Working',
                        'completed' => 'Completed Day',
                        'remote' => 'Remote Work',
                        'half_day' => 'Half Day',
                    ]),

                SelectFilter::make('location_mode')
                    ->label('Location Mode')
                    ->options([
                        'office' => 'Office Geofence',
                        'remote' => 'Remote / WFH',
                    ]),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')->label('From Date'),
                        DatePicker::make('date_to')->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date_from'], fn ($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['date_to'], fn ($q, $date) => $q->whereDate('date', '<=', $date));
                    }),
            ])
            ->actions([
                Action::make('correct_attendance')
                    ->label('Correct')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn (): bool => auth()->user()?->hasRole('super_admin') || auth()->user()?->hasRole('attendance_manager') || auth()->user()?->can('Correct:StaffAttendance'))
                    ->form([
                        DateTimePicker::make('punch_in_at')
                            ->label('Corrected Punch In Time')
                            ->default(fn (StaffAttendance $record) => $record->punch_in_at),

                        DateTimePicker::make('punch_out_at')
                            ->label('Corrected Punch Out Time')
                            ->default(fn (StaffAttendance $record) => $record->punch_out_at),

                        Textarea::make('correction_reason')
                            ->label('Reason for Correction (Mandatory)')
                            ->required()
                            ->rows(3)
                            ->placeholder('e.g. Device battery died at 5:00 PM, verified with supervisor.'),
                    ])
                    ->action(function (StaffAttendance $record, array $data): void {
                        app(StaffAttendanceService::class)->manualCorrection($record, auth()->user(), $data);

                        Notification::make()
                            ->title('Attendance Corrected')
                            ->body("Attendance for {$record->user->name} on {$record->date->format('M d, Y')} has been updated with audit trail.")
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn (): bool => (bool) auth()->user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => (bool) auth()->user()?->hasRole('super_admin')),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
}
