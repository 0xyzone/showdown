<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Enums\MarketingPlatform;
use App\Models\CampaignDeliverable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class CampaignInfolist
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
                                Section::make('Campaign Overview')
                                    ->icon('heroicon-o-megaphone')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('title')
                                                ->size(TextSize::Large)
                                                ->weight('bold'),
                                            TextEntry::make('campaign_code')
                                                ->badge()
                                                ->color('gray'),
                                        ]),
                                        TextEntry::make('objectives')
                                            ->placeholder('No objectives stated.'),
                                        TextEntry::make('target_audience')
                                            ->placeholder('No target audience specified.'),
                                    ]),
                                Section::make('Deliverables Checklist')
                                    ->icon('heroicon-o-clipboard-document-check')
                                    ->schema([
                                        RepeatableEntry::make('deliverables')
                                            ->schema([
                                                Grid::make(4)->schema([
                                                    TextEntry::make('title')
                                                        ->weight('bold')
                                                        ->columnSpan(2),
                                                    TextEntry::make('creative_type')
                                                        ->badge(),
                                                    TextEntry::make('approval_status')
                                                        ->badge(),
                                                ]),
                                                Grid::make(3)->schema([
                                                    TextEntry::make('platforms')
                                                        ->label('Platforms')
                                                        ->badge()
                                                        ->formatStateUsing(function ($state, CampaignDeliverable $record) {
                                                            $platforms = $record->platforms;
                                                            if (! is_array($platforms) || empty($platforms)) {
                                                                return 'Unspecified';
                                                            }

                                                            return array_map(function ($p) {
                                                                $enum = $p instanceof MarketingPlatform ? $p : MarketingPlatform::tryFrom((string) $p);

                                                                return $enum ? $enum->getLabel() : ucfirst((string) $p);
                                                            }, $platforms);
                                                        }),
                                                    TextEntry::make('scheduled_at')
                                                        ->dateTime('M d, Y h:i A')
                                                        ->placeholder('Unscheduled'),
                                                    TextEntry::make('assignee.name')
                                                        ->label('Assigned To')
                                                        ->placeholder('Unassigned'),
                                                ]),
                                                TextEntry::make('copy_text')
                                                    ->label('Copy / Caption')
                                                    ->placeholder('No copy text.')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Status & Priority')
                                    ->schema([
                                        TextEntry::make('status')
                                            ->badge(),
                                        TextEntry::make('priority')
                                            ->badge(),
                                        TextEntry::make('owner.name')
                                            ->label('Campaign Manager')
                                            ->placeholder('Unassigned'),
                                    ]),
                                Section::make('Schedule')
                                    ->schema([
                                        TextEntry::make('start_date')
                                            ->date('M d, Y'),
                                        TextEntry::make('end_date')
                                            ->date('M d, Y'),
                                    ]),
                                Section::make('Financials & Metrics')
                                    ->schema([
                                        TextEntry::make('budget')
                                            ->money('NPR'),
                                        TextEntry::make('actual_spend')
                                            ->money('NPR'),
                                        TextEntry::make('total_impressions')
                                            ->label('Impressions')
                                            ->numeric(),
                                        TextEntry::make('total_reach')
                                            ->label('Reach')
                                            ->numeric(),
                                        TextEntry::make('total_conversions')
                                            ->label('Conversions')
                                            ->numeric(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
