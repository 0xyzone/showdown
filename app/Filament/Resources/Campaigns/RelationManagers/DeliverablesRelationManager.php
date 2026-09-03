<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\DeliverableApprovalStatus;
use App\Enums\DeliverableType;
use App\Enums\MarketingPlatform;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeliverablesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliverables';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('creative_type')
                        ->label('Creative Type')
                        ->options(DeliverableType::class)
                        ->required(),

                    Select::make('platform')
                        ->label('Target Platform')
                        ->options(MarketingPlatform::class)
                        ->required(),

                    DateTimePicker::make('scheduled_at')
                        ->label('Scheduled Publish Time')
                        ->native(false),

                    Select::make('approval_status')
                        ->label('Approval Status')
                        ->options(DeliverableApprovalStatus::class)
                        ->default(DeliverableApprovalStatus::PendingReview->value)
                        ->required(),

                    Select::make('assigned_to')
                        ->label('Assigned Designer / Content Creator')
                        ->options(User::query()->pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    TextInput::make('spend')
                        ->label('Deliverable Budget / Spend (NPR)')
                        ->numeric()
                        ->prefix('Rs.')
                        ->default(0),

                    Textarea::make('copy_text')
                        ->label('Copy / Captions')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('designer_notes')
                        ->label('Designer / Production Notes')
                        ->rows(2)
                        ->columnSpanFull(),

                    FileUpload::make('asset_files')
                        ->label('Creative Assets & Mockups')
                        ->multiple()
                        ->directory('campaign-deliverables')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('creative_type')
                    ->badge(),

                TextColumn::make('platform')
                    ->badge(),

                SelectColumn::make('approval_status')
                    ->label('Status')
                    ->options(
                        collect(DeliverableApprovalStatus::cases())
                            ->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()])
                            ->toArray()
                    ),

                TextColumn::make('scheduled_at')
                    ->dateTime('M d, Y h:i A')
                    ->placeholder('Unscheduled')
                    ->sortable(),

                TextColumn::make('assignee.name')
                    ->label('Creator')
                    ->placeholder('Unassigned'),

                TextColumn::make('impressions')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reach')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('creative_type')
                    ->options(DeliverableType::class),
                SelectFilter::make('platform')
                    ->options(MarketingPlatform::class),
                SelectFilter::make('approval_status')
                    ->options(DeliverableApprovalStatus::class),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
