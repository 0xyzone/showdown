<?php

namespace App\Filament\Resources\StaffAttendanceProfiles;

use App\Filament\Resources\StaffAttendanceProfiles\Pages\EditStaffAttendanceProfile;
use App\Filament\Resources\StaffAttendanceProfiles\Pages\ListStaffAttendanceProfiles;
use App\Models\StaffAttendanceProfile;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StaffAttendanceProfileResource extends Resource
{
    protected static ?string $model = StaffAttendanceProfile::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Staff Attendance Policies';

    protected static string|\UnitEnum|null $navigationGroup = 'Staff & Attendance';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Staff Attendance Policy Configuration')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->label('Staff Member')
                                ->relationship('user', 'name')
                                ->disabled()
                                ->required(),

                            Select::make('attendance_mode')
                                ->label('Attendance Location Policy')
                                ->options([
                                    'office_only' => 'Office Only (Requires GPS Geofence Check)',
                                    'remote_allowed' => 'Remote Allowed (Work-From-Home Exempt)',
                                    'office_and_network' => 'Office + Approved Network IP',
                                    'flexible' => 'Flexible (Office or Remote)',
                                ])
                                ->required()
                                ->helperText('Control whether this staff member is exempt from office geofencing.'),

                            Toggle::make('is_biometric_exempt')
                                ->label('Biometric / Passkey Exemption')
                                ->helperText('Allow staff to punch in/out without requiring a registered WebAuthn biometric passkey.'),

                            Textarea::make('notes')
                                ->label('Administrative Notes')
                                ->placeholder('e.g. Approved for permanent remote work by HR.')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('attendance_mode')
                    ->label('Policy Mode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'remote_allowed' => 'primary',
                        'office_only' => 'success',
                        'office_and_network' => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'remote_allowed' => 'Remote Allowed (WFH)',
                        'office_only' => 'Office Only',
                        'office_and_network' => 'Office + Network',
                        default => ucfirst($state),
                    }),

                IconColumn::make('is_biometric_exempt')
                    ->label('Biometric Exempt')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('success'),

                TextColumn::make('registered_devices')
                    ->label('Registered Devices')
                    ->state(fn (StaffAttendanceProfile $record): int => $record->user?->biometricCredentials()->where('is_active', true)->count() ?? 0)
                    ->badge()
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('attendance_mode')
                    ->label('Policy Mode')
                    ->options([
                        'office_only' => 'Office Only',
                        'remote_allowed' => 'Remote Allowed',
                        'office_and_network' => 'Office + Network',
                        'flexible' => 'Flexible',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffAttendanceProfiles::route('/'),
            'edit' => EditStaffAttendanceProfile::route('/{record}/edit'),
        ];
    }
}
