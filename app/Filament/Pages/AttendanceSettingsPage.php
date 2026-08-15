<?php

namespace App\Filament\Pages;

use App\Models\AttendanceSetting;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Attendance Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Staff & Attendance';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.attendance-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = AttendanceSetting::current();
        $this->form->fill([
            'office_name' => $setting->office_name,
            'office_latitude' => $setting->office_latitude,
            'office_longitude' => $setting->office_longitude,
            'allowed_radius_meters' => $setting->allowed_radius_meters,
            'max_gps_accuracy_meters' => $setting->max_gps_accuracy_meters,
            'require_biometric' => $setting->require_biometric,
            'max_devices_per_user' => $setting->max_devices_per_user,
            'is_network_restriction_enabled' => $setting->is_network_restriction_enabled,
            'allowed_ip_addresses' => $setting->allowed_ip_addresses ?: [],
        ]);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->hasRole('attendance_manager') || $user->can('Update:AttendanceSetting');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Office Location & Geofencing')
                    ->description('Define the physical office coordinates and the radius within which staff attendance is permitted.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('office_name')
                                ->label('Office Name / Branch')
                                ->required()
                                ->placeholder('e.g. Showdown Main HQ'),

                            TextInput::make('allowed_radius_meters')
                                ->label('Allowed Geofence Radius')
                                ->numeric()
                                ->required()
                                ->suffix('meters')
                                ->helperText('Staff must be within this distance to punch in.'),

                            TextInput::make('office_latitude')
                                ->label('Office Latitude')
                                ->numeric()
                                ->required()
                                ->placeholder('e.g. 27.7172453'),

                            TextInput::make('office_longitude')
                                ->label('Office Longitude')
                                ->numeric()
                                ->required()
                                ->placeholder('e.g. 85.3239605'),

                            TextInput::make('max_gps_accuracy_meters')
                                ->label('Maximum GPS Accuracy Threshold')
                                ->numeric()
                                ->required()
                                ->suffix('meters')
                                ->helperText('Rejects GPS readings with uncertainty larger than this threshold.'),
                        ]),
                    ]),

                Section::make('Biometric & Device Authentication Rules')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('require_biometric')
                                ->label('Enforce Biometric Passkeys (WebAuthn)')
                                ->helperText('Require staff to authenticate using fingerprint / Face ID / device PIN.')
                                ->columnSpanFull(),

                            TextInput::make('max_devices_per_user')
                                ->label('Max Registered Devices Per Staff')
                                ->numeric()
                                ->required()
                                ->default(3)
                                ->helperText('Maximum number of WebAuthn passkey credentials a user can register.'),
                        ]),
                    ]),

                Section::make('Optional Office Network / IP Restrictions')
                    ->description('Optionally restrict attendance to specific office IP addresses or CIDR blocks.')
                    ->schema([
                        Toggle::make('is_network_restriction_enabled')
                            ->label('Enable Office IP Verification')
                            ->helperText('When enabled, office-bound staff requests must originate from approved IP addresses.')
                            ->columnSpanFull(),

                        TagsInput::make('allowed_ip_addresses')
                            ->label('Approved Office IP Addresses / Subnets')
                            ->placeholder('Add IP (e.g. 103.145.20.10, 192.168.1.0/24)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = AttendanceSetting::current();
        $setting->update($data);

        Notification::make()
            ->title('Attendance Settings Saved')
            ->body('Office location and attendance rules have been successfully updated.')
            ->success()
            ->send();
    }
}
