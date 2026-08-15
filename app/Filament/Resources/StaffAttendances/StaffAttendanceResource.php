<?php

namespace App\Filament\Resources\StaffAttendances;

use App\Filament\Resources\StaffAttendances\Pages\ListStaffAttendances;
use App\Filament\Resources\StaffAttendances\Pages\ViewStaffAttendance;
use App\Filament\Resources\StaffAttendances\Tables\StaffAttendancesTable;
use App\Models\StaffAttendance;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StaffAttendanceResource extends Resource
{
    protected static ?string $model = StaffAttendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Staff Attendance';

    protected static string|\UnitEnum|null $navigationGroup = 'Staff & Attendance';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance Record Overview')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('user.name')
                                ->label('Staff Member')
                                ->disabled(),
                            TextInput::make('date')
                                ->label('Date')
                                ->disabled(),
                            TextInput::make('status')
                                ->label('Status')
                                ->disabled(),
                            DateTimePicker::make('punch_in_at')
                                ->label('Punch In Time')
                                ->disabled(),
                            DateTimePicker::make('punch_out_at')
                                ->label('Punch Out Time')
                                ->disabled(),
                            TextInput::make('formatted_worked_time')
                                ->label('Total Worked Duration')
                                ->disabled(),
                        ]),
                    ]),

                Section::make('Verification & Geolocation Audit')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('location_mode')
                                ->label('Location Mode')
                                ->disabled(),
                            TextInput::make('punch_in_distance_meters')
                                ->label('Punch-In Distance From Office')
                                ->suffix('meters')
                                ->disabled(),
                            TextInput::make('punch_in_accuracy')
                                ->label('GPS Accuracy')
                                ->suffix('meters')
                                ->disabled(),
                            TextInput::make('punch_in_ip')
                                ->label('Punch-In IP Address')
                                ->disabled(),
                            TextInput::make('punch_in_method')
                                ->label('Verification Method')
                                ->disabled(),
                            Toggle::make('punch_in_verified_biometric')
                                ->label('Biometric Passkey Verified')
                                ->disabled(),
                        ]),
                    ]),

                Section::make('Manual Correction Audit')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('is_manually_corrected')
                                ->label('Was Manually Adjusted')
                                ->disabled(),
                            TextInput::make('correctedByAdmin.name')
                                ->label('Adjusted By Admin')
                                ->disabled(),
                            DateTimePicker::make('corrected_at')
                                ->label('Adjustment Timestamp')
                                ->disabled(),
                            Textarea::make('correction_reason')
                                ->label('Adjustment Reason')
                                ->disabled()
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->visible(fn (StaffAttendance $record): bool => (bool) $record->is_manually_corrected),
            ]);
    }

    public static function table(Table $table): Table
    {
        return StaffAttendancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffAttendances::route('/'),
            'view' => ViewStaffAttendance::route('/{record}'),
        ];
    }
}
