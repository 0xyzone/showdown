<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(Auth::id()),
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Section::make('Classification')
                                    ->icon('heroicon-o-tag')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('lead_type_id')
                                                ->label('Lead Type')
                                                ->relationship('lead_type', 'name')
                                                ->preload()
                                                ->searchable()
                                                ->createOptionForm([
                                                    TextInput::make('name')
                                                        ->required(),
                                                ])
                                                ->required(),
                                            Select::make('lead_status_id')
                                                ->label('Status')
                                                ->relationship('lead_status', 'name')
                                                ->preload()
                                                ->searchable()
                                                ->createOptionForm([
                                                    TextInput::make('name')
                                                        ->required(),
                                                ])
                                                ->required(),
                                        ]),
                                    ]),
                                Section::make('Company & Contact')
                                    ->icon('heroicon-o-building-office-2')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('company_name')
                                                ->label('Company / Organization')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('contact_name')
                                                ->label('Contact Person')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('phone')
                                                ->label('Phone Number')
                                                ->tel()
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('email')
                                                ->label('Email Address')
                                                ->email()
                                                ->maxLength(255),
                                        ]),
                                    ]),
                                Section::make('Location & Notes')
                                    ->icon('heroicon-o-map-pin')
                                    ->schema([
                                        Grid::make(1)->schema([
                                            Textarea::make('address')
                                                ->label('Location / Address')
                                                ->rows(2),
                                            TextInput::make('gmap_link')
                                                ->label('Google Maps Link')
                                                ->url()
                                                ->placeholder('https://maps.google.com/...'),
                                            Textarea::make('notes')
                                                ->label('General Lead Notes')
                                                ->rows(3),
                                        ]),
                                    ]),
                            ])
                            ->columnSpan(1),
                        Grid::make(1)
                            ->schema([
                                Section::make('Scheduled Meetings & Google Calendar')
                                    ->icon('heroicon-o-calendar-days')
                                    ->description('Schedule meetings synced directly with your Google Calendar.')
                                    ->schema([
                                        Placeholder::make('google_calendar_status')
                                            ->label('')
                                            ->content(function () {
                                                /** @var User|null $user */
                                                $user = Auth::user();
                                                $isConnected = $user?->isGoogleCalendarConnected() ?? false;
                                                $email = $user?->getGoogleCalendarEmail();

                                                if ($isConnected) {
                                                    return new HtmlString('
                                                        <div class="flex items-center justify-between p-3 mb-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400">
                                                            <div class="flex items-center gap-2 text-sm">
                                                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <span><strong>Google Calendar Linked:</strong> '.e($email ?: 'Connected').'</span>
                                                            </div>
                                                            <a href="'.route('google.calendar.disconnect').'" class="text-xs text-red-500 underline hover:text-red-600 font-semibold">Disconnect</a>
                                                        </div>
                                                    ');
                                                }

                                                return new HtmlString('
                                                    <div class="flex items-center justify-between p-3 mb-2 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-300">
                                                        <div class="flex items-center gap-2 text-xs sm:text-sm">
                                                            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                            <span>Link your Google account to auto-sync meetings & create Google Meet links.</span>
                                                        </div>
                                                        <a href="'.route('google.calendar.redirect').'" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-white bg-primary-600 rounded-md hover:bg-primary-500 shadow-sm shrink-0">
                                                            Link Google
                                                        </a>
                                                    </div>
                                                ');
                                            })
                                            ->columnSpanFull(),
                                        Repeater::make('meetings')
                                            ->relationship('meetings')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    TextInput::make('title')
                                                        ->label('Meeting Title / Agenda')
                                                        ->placeholder('e.g. Discovery Pitch, Proposal Review')
                                                        ->required()
                                                        ->columnSpanFull(),
                                                    DateTimePicker::make('meeting_start')
                                                        ->label('Start Date & Time')
                                                        ->default(now()->addDay()->setHour(14)->setMinute(0))
                                                        ->required()
                                                        ->native(false),
                                                    DateTimePicker::make('meeting_end')
                                                        ->label('End Date & Time')
                                                        ->default(now()->addDay()->setHour(15)->setMinute(0))
                                                        ->required()
                                                        ->native(false),
                                                    Select::make('meeting_location_type')
                                                        ->label('Meeting Type')
                                                        ->options([
                                                            'online_meet' => 'Google Meet / Online',
                                                            'in_person' => 'In-Person / Office',
                                                            'phone' => 'Phone Call',
                                                        ])
                                                        ->default('online_meet')
                                                        ->required(),
                                                    Select::make('status')
                                                        ->label('Meeting Status')
                                                        ->options([
                                                            'scheduled' => 'Scheduled',
                                                            'completed' => 'Completed',
                                                            'cancelled' => 'Cancelled',
                                                            'rescheduled' => 'Rescheduled',
                                                        ])
                                                        ->default('scheduled')
                                                        ->required(),
                                                    TextInput::make('meeting_link')
                                                        ->label('Meeting / Conference Link')
                                                        ->placeholder('Auto-generated from Google Meet or custom link')
                                                        ->url()
                                                        ->columnSpanFull(),
                                                    Textarea::make('notes')
                                                        ->label('Meeting Agenda & Preparation Notes')
                                                        ->placeholder('Discussion agenda, key objectives, participants...')
                                                        ->autosize()
                                                        ->columnSpanFull(),
                                                    Hidden::make('user_id')
                                                        ->default(fn () => Auth::id())
                                                        ->dehydrated(),
                                                ]),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => isset($state['title']) ? ($state['title'].' ('.(isset($state['meeting_start']) ? date('M d, H:i', strtotime($state['meeting_start'])) : '').')') : 'New Scheduled Meeting')
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('Schedule Meeting')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Follow-ups Tracking')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->description('Track historical follow-up dates, outcomes, and remarks.')
                                    ->schema([
                                        Repeater::make('followups')
                                            ->relationship('followups')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    DatePicker::make('followup_date')
                                                        ->label('Follow-up Date')
                                                        ->default(now())
                                                        ->required()
                                                        ->native(false),
                                                    Select::make('user_id')
                                                        ->label('Followed up by')
                                                        ->relationship('user', 'name')
                                                        ->default(fn () => Auth::id())
                                                        ->searchable()
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->preload(),
                                                    Textarea::make('remarks')
                                                        ->label('Remarks & Outcomes')
                                                        ->placeholder('Enter follow-up discussion points, next action items...')
                                                        ->required()
                                                        ->autosize()
                                                        ->columnSpanFull(),
                                                ]),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => isset($state['followup_date']) ? 'Follow-up: '.date('M d, Y', strtotime($state['followup_date'])) : 'New Follow-up')
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Follow-up')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
