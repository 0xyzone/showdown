<?php

namespace App\Filament\Resources\SponsorQueries\Tables;

use App\Mail\SponsorQueryAcknowledged;
use App\Mail\SponsorQueryConverted;
use App\Models\Partner;
use App\Models\Sponsor;
use App\Models\SponsorQuery;
use App\Models\Tournament;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class SponsorQueriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Company / Brand')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label('Contact Person')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'contacted' => 'info',
                        'converted' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'contacted' => 'Contacted',
                        'converted' => 'Converted',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('acknowledge')
                    ->label('Follow Up')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->schema([
                        Textarea::make('message')
                            ->label('Custom Follow-up Email Message')
                            ->placeholder('Enter custom note or next steps for the prospect...')
                            ->rows(4),
                    ])
                    ->action(function (array $data, SponsorQuery $record): void {
                        $record->update(['status' => 'contacted']);

                        try {
                            Mail::to($record->email)->send(new SponsorQueryAcknowledged($record, $data['message'] ?? ''));
                        } catch (\Throwable $e) {
                        }

                        Notification::make()
                            ->success()
                            ->title('Follow-up email sent to '.$record->email)
                            ->send();
                    }),

                Action::make('convert')
                    ->label('Convert')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('success')
                    ->visible(fn (SponsorQuery $record): bool => $record->status !== 'converted')
                    ->schema([
                        Select::make('target_type')
                            ->label('Convert Query To')
                            ->options([
                                'sponsor' => 'Official Sponsor',
                                'partner' => 'Official Partner',
                            ])
                            ->required()
                            ->live(),

                        Select::make('tournament_id')
                            ->label('Assigned Tournament')
                            ->options(Tournament::pluck('name', 'id'))
                            ->nullable()
                            ->placeholder('All Tournaments (Global)'),
                        // Sponsor Fields
                        Select::make('sponsor_level')
                            ->label('Sponsorship Tier Level')
                            ->options([
                                'title' => 'Title Sponsor (Mega Spotlight)',
                                'platinum' => 'Platinum Sponsor (Large Spotlight)',
                                'gold' => 'Gold Sponsor (Medium Spotlight)',
                                'silver' => 'Silver Sponsor (Standard Stream)',
                            ])
                            ->visible(fn (Get $get): bool => $get('target_type') === 'sponsor')
                            ->required(fn (Get $get): bool => $get('target_type') === 'sponsor'),

                        // Partner Fields
                        TextInput::make('partner_title')
                            ->label('Partner Category Title')
                            ->placeholder('e.g. Media Partner, Hospitality Partner, Gaming Gear Partner')
                            ->visible(fn (Get $get): bool => $get('target_type') === 'partner')
                            ->required(fn (Get $get): bool => $get('target_type') === 'partner'),
                        Select::make('partner_level')
                            ->label('Partner Level')
                            ->options([
                                'major' => 'Major Partner',
                                'standard' => 'Standard Partner',
                            ])
                            ->visible(fn (Get $get): bool => $get('target_type') === 'partner')
                            ->required(fn (Get $get): bool => $get('target_type') === 'partner'),

                        // Common Fields
                        TextInput::make('website_url')
                            ->label('Brand Website URL')
                            ->url(),
                        FileUpload::make('logo_url')
                            ->label('Brand Logo Image')
                            ->image()
                            ->disk('public')
                            ->directory('sponsors')
                            ->visibility('public')
                            ->required(),
                    ])
                    ->action(function (array $data, SponsorQuery $record): void {
                        if ($data['target_type'] === 'sponsor') {
                            $sponsor = Sponsor::create([
                                'name' => $record->company_name,
                                'tournament_id' => $data['tournament_id'] ?? null,
                                'logo_url' => $data['logo_url'],
                                'website_url' => $data['website_url'] ?? null,
                                'level' => $data['sponsor_level'],
                                'sponsor_query_id' => $record->id,
                                'is_active' => true,
                            ]);

                            $record->update([
                                'status' => 'converted',
                                'converted_type' => Sponsor::class,
                                'converted_id' => $sponsor->id,
                            ]);

                            try {
                                Mail::to($record->email)->send(new SponsorQueryConverted(
                                    $record,
                                    'Official Sponsor',
                                    strtoupper($data['sponsor_level']).' Sponsor Tier'
                                ));
                            } catch (\Throwable $e) {
                            }

                            Notification::make()
                                ->success()
                                ->title('Query converted & confirmation email sent to sponsor!')
                                ->send();
                        } elseif ($data['target_type'] === 'partner') {
                            $partner = Partner::create([
                                'name' => $record->company_name,
                                'tournament_id' => $data['tournament_id'] ?? null,
                                'title' => $data['partner_title'],
                                'logo_url' => $data['logo_url'],
                                'website_url' => $data['website_url'] ?? null,
                                'level' => $data['partner_level'],
                                'sponsor_query_id' => $record->id,
                                'is_active' => true,
                            ]);

                            $record->update([
                                'status' => 'converted',
                                'converted_type' => Partner::class,
                                'converted_id' => $partner->id,
                            ]);

                            try {
                                Mail::to($record->email)->send(new SponsorQueryConverted(
                                    $record,
                                    'Official Partner',
                                    $data['partner_title']
                                ));
                            } catch (\Throwable $e) {
                            }

                            Notification::make()
                                ->success()
                                ->title('Query converted & confirmation email sent to partner!')
                                ->send();
                        }
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
