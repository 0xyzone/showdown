<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\LeadStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('contact_name')
                    ->label('Contact')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->phone),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('lead_type.name')
                    ->label('Type')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state),
                SelectColumn::make('lead_status_id')
                    ->label('Status')
                    ->options(
                        LeadStatus::all()->pluck('name', 'id')
                    )
                    ->searchable(),
                TextColumn::make('latestFollowup.followup_date')
                    ->label('Last Follow-up')
                    ->date('M d, Y')
                    ->placeholder('None yet')
                    ->badge()
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    ->sortable(),
                TextColumn::make('followups_count')
                    ->counts('followups')
                    ->label('Follow-ups')
                    ->badge()
                    ->color('secondary')
                    ->alignCenter(),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('lead_type_id')
                    ->label('Lead Type')
                    ->relationship('lead_type', 'name'),
                SelectFilter::make('lead_status_id')
                    ->label('Status')
                    ->relationship('lead_status', 'name'),
            ])
            ->poll('10s')
            ->recordActions([
                ViewAction::make()
                    ->slideOver()
                    ->modalWidth('7xl'),
                EditAction::make()
                    ->slideOver()
                    ->modalWidth('7xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
