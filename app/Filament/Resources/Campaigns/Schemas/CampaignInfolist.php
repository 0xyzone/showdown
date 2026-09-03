<?php

namespace App\Filament\Resources\Campaigns\Schemas;

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
                                        TextEntry::make('title')
                                            ->weight('bold')
                                            ->size(TextSize::Large),
                                        Grid::make(2)->schema([
                                            TextEntry::make('campaign_code')
                                                ->label('Code')
                                                ->badge()
                                                ->color('gray'),
                                            TextEntry::make('slug')
                                                ->label('Slug')
                                                ->color('secondary'),
                                        ]),
                                        TextEntry::make('objectives')
                                            ->label('Objectives')
                                            ->placeholder('No specific objectives entered.')
                                            ->columnSpanFull(),
                                        TextEntry::make('target_audience')
                                            ->label('Target Audience')
                                            ->placeholder('Not specified.')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Deliverables')
                                    ->icon('heroicon-o-document-duplicate')
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
                                                    TextEntry::make('platform')
                                                        ->badge(),
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
