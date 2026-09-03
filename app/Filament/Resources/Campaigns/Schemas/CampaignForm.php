<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Enums\CampaignPriority;
use App\Enums\CampaignStatus;
use App\Enums\DeliverableApprovalStatus;
use App\Enums\DeliverableType;
use App\Enums\MarketingPlatform;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->schema([
                                Section::make('General Information')
                                    ->description('Core campaign identity, goals, and schedule boundaries.')
                                    ->icon('heroicon-o-megaphone')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Campaign Title')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                                if ($state) {
                                                    $set('slug', Str::slug($state));
                                                }
                                            }),

                                        Grid::make(2)->schema([
                                            TextInput::make('campaign_code')
                                                ->label('Campaign Code')
                                                ->required()
                                                ->default(fn () => 'CMP-'.strtoupper(Str::random(6)))
                                                ->unique(ignoreRecord: true),

                                            TextInput::make('slug')
                                                ->label('URL Slug')
                                                ->required()
                                                ->unique(ignoreRecord: true),
                                        ]),

                                        Textarea::make('objectives')
                                            ->label('Objectives & Goals')
                                            ->rows(3)
                                            ->placeholder('E.g., Increase brand awareness, acquire 5,000 new event signups, launch tournament teaser...'),

                                        Textarea::make('target_audience')
                                            ->label('Target Audience / Persona')
                                            ->rows(2)
                                            ->placeholder('E.g., Esports fans, college gamers aged 18-28 across Nepal & South Asia'),
                                    ]),

                                Section::make('Deliverables & Creative Items')
                                    ->description('Plan posts, reels, ads, and graphic assets for this campaign.')
                                    ->icon('heroicon-o-document-duplicate')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('deliverables')
                                            ->relationship('deliverables')
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    TextInput::make('title')
                                                        ->label('Deliverable Title')
                                                        ->required()
                                                        ->columnSpan(2),

                                                    Select::make('creative_type')
                                                        ->label('Creative Type')
                                                        ->options(DeliverableType::class)
                                                        ->required(),
                                                ]),

                                                Grid::make(3)->schema([
                                                    Select::make('platform')
                                                        ->label('Target Platform')
                                                        ->options(MarketingPlatform::class)
                                                        ->required(),

                                                    DateTimePicker::make('scheduled_at')
                                                        ->label('Scheduled Time')
                                                        ->native(false),

                                                    Select::make('approval_status')
                                                        ->label('Approval Status')
                                                        ->options(DeliverableApprovalStatus::class)
                                                        ->default(DeliverableApprovalStatus::PendingReview->value)
                                                        ->required(),
                                                ]),

                                                Grid::make(2)->schema([
                                                    Select::make('assigned_to')
                                                        ->label('Assignee / Creator')
                                                        ->options(User::query()->pluck('name', 'id'))
                                                        ->searchable()
                                                        ->preload(),

                                                    TextInput::make('spend')
                                                        ->label('Deliverable Spend / Budget (NPR)')
                                                        ->numeric()
                                                        ->prefix('Rs.')
                                                        ->default(0),
                                                ]),

                                                Textarea::make('copy_text')
                                                    ->label('Copy / Captions & Hashtags')
                                                    ->rows(2),

                                                Textarea::make('designer_notes')
                                                    ->label('Designer / Production Notes')
                                                    ->rows(2),

                                                FileUpload::make('asset_files')
                                                    ->label('Creative Attachments / Mockups')
                                                    ->multiple()
                                                    ->maxFiles(5)
                                                    ->directory('campaign-assets')
                                                    ->columnSpanFull(),
                                            ])
                                            ->collapsed()
                                            ->itemLabel(function (array $state): ?string {
                                                $platform = $state['platform'] ?? null;
                                                $platformLabel = match (true) {
                                                    $platform instanceof MarketingPlatform => $platform->getLabel() ?? $platform->value,
                                                    is_string($platform) => MarketingPlatform::tryFrom($platform)?->getLabel() ?? $platform,
                                                    default => 'General',
                                                };

                                                return ($state['title'] ?? 'New Deliverable').' ('.$platformLabel.')';
                                            })
                                            ->addActionLabel('Add Deliverable Item'),
                                    ]),
                            ]),

                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Status & Pipeline')
                                    ->icon('heroicon-o-adjustments-horizontal')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Pipeline Status')
                                            ->options(CampaignStatus::class)
                                            ->default(CampaignStatus::Draft->value)
                                            ->required(),

                                        Select::make('priority')
                                            ->label('Priority Level')
                                            ->options(CampaignPriority::class)
                                            ->default(CampaignPriority::Medium->value)
                                            ->required(),

                                        Select::make('owner_id')
                                            ->label('Campaign Manager / Owner')
                                            ->options(User::query()->pluck('name', 'id'))
                                            ->default(fn () => Auth::id())
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Select::make('teamMembers')
                                            ->label('Team Members')
                                            ->relationship('teamMembers', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                    ]),

                                Section::make('Timeline & Schedule')
                                    ->icon('heroicon-o-calendar-days')
                                    ->schema([
                                        DatePicker::make('start_date')
                                            ->label('Start Date')
                                            ->required()
                                            ->native(false),

                                        DatePicker::make('end_date')
                                            ->label('End Date')
                                            ->required()
                                            ->afterOrEqual('start_date')
                                            ->native(false),
                                    ]),

                                Section::make('Budget & Financials')
                                    ->icon('heroicon-o-banknotes')
                                    ->schema([
                                        TextInput::make('budget')
                                            ->label('Total Budget (NPR)')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->default(0)
                                            ->required(),

                                        TextInput::make('actual_spend')
                                            ->label('Actual Spend (NPR)')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->default(0),
                                    ]),

                                Section::make('Channels & Tags')
                                    ->icon('heroicon-o-tag')
                                    ->schema([
                                        Select::make('platforms')
                                            ->label('Campaign Platforms')
                                            ->multiple()
                                            ->options(MarketingPlatform::class)
                                            ->preload(),

                                        TagsInput::make('tags')
                                            ->label('Tags & Categories')
                                            ->placeholder('Type and hit enter...'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
