<?php

namespace App\Filament\Resources\StaffPunchEvents;

use App\Filament\Resources\StaffPunchEvents\Pages\ListStaffPunchEvents;
use App\Filament\Resources\StaffPunchEvents\Pages\ViewStaffPunchEvent;
use App\Filament\Resources\StaffPunchEvents\Tables\StaffPunchEventsTable;
use App\Models\StaffPunchEvent;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StaffPunchEventResource extends Resource
{
    protected static ?string $model = StaffPunchEvent::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Punch Security Audit';

    protected static string|\UnitEnum|null $navigationGroup = 'Staff & Attendance';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Punch Security Event Audit')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('user.name')
                                ->label('Staff Member')
                                ->disabled(),
                            TextInput::make('event_type')
                                ->label('Event Type')
                                ->disabled(),
                            TextInput::make('status')
                                ->label('Result Status')
                                ->disabled(),
                            DateTimePicker::make('occurred_at')
                                ->label('Timestamp')
                                ->disabled(),
                            TextInput::make('distance_meters')
                                ->label('Distance From Office')
                                ->suffix('meters')
                                ->disabled(),
                            TextInput::make('accuracy')
                                ->label('GPS Accuracy')
                                ->suffix('meters')
                                ->disabled(),
                            TextInput::make('ip_address')
                                ->label('IP Address')
                                ->disabled(),
                            TextInput::make('verification_method')
                                ->label('Verification Method')
                                ->disabled(),
                            TextInput::make('failure_reason')
                                ->label('Failure Reason')
                                ->disabled(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return StaffPunchEventsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffPunchEvents::route('/'),
            'view' => ViewStaffPunchEvent::route('/{record}'),
        ];
    }
}
