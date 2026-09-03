<?php

namespace App\Filament\Resources\Campaigns\Tables;

use App\Enums\CampaignPriority;
use App\Enums\CampaignStatus;
use App\Enums\MarketingPlatform;
use App\Models\Campaign;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campaign_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight(FontWeight::SemiBold)
                    ->color('gray'),

                TextColumn::make('title')
                    ->label('Campaign')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Campaign $record): string => StrLimit($record->objectives ?? 'No objective set', 45)),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->sortable(),

                TextColumn::make('platforms')
                    ->label('Platforms')
                    ->badge()
                    ->state(function (Campaign $record): array {
                        if (! is_array($record->platforms)) {
                            return [];
                        }

                        return array_map(function ($platform) {
                            $enum = MarketingPlatform::tryFrom($platform);

                            return $enum ? $enum->getLabel() : ucfirst($platform);
                        }, $record->platforms);
                    })
                    ->color('info')
                    ->wrap(),

                TextColumn::make('start_date')
                    ->label('Dates')
                    ->date('M d, Y')
                    ->description(fn (Campaign $record): string => 'to '.($record->end_date ? $record->end_date->format('M d, Y') : 'N/A'))
                    ->sortable(),

                TextColumn::make('budget')
                    ->label('Budget')
                    ->money('NPR')
                    ->description(fn (Campaign $record): string => 'Spent: Rs. '.number_format($record->actual_spend, 2))
                    ->sortable(),

                TextColumn::make('deliverables_count')
                    ->counts('deliverables')
                    ->label('Deliverables')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                TextColumn::make('owner.name')
                    ->label('Manager')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CampaignStatus::class),

                SelectFilter::make('priority')
                    ->options(CampaignPriority::class),

                Filter::make('active_now')
                    ->label('Currently Running')
                    ->query(fn (Builder $query) => $query->where('status', CampaignStatus::Running->value)),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('Running After'),
                        DatePicker::make('until')->label('Running Before'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

function StrLimit(?string $value, int $limit = 100): string
{
    return Str::limit($value ?? '', $limit);
}
