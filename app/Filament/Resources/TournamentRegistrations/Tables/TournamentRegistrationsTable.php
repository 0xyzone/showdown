<?php

namespace App\Filament\Resources\TournamentRegistrations\Tables;

use App\Mail\RegistrationStatusUpdatedMail;
use App\Models\TournamentRegistration;
use App\Services\DiscordService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class TournamentRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('team.logo_path')
                    ->label('Crest')
                    ->disk('public')
                    ->square(),
                TextColumn::make('tournament.name')
                    ->label('Tournament')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('team.name')
                    ->label('Team Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('team.gameTitle.name')
                    ->label('Game Title')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('registeredBy.name')
                    ->label('Manager')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'disqualified' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                ImageColumn::make('payment_receipt_path')
                    ->label('Receipt')
                    ->disk('public'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y H:i'),
            ])
            ->filters([
                SelectFilter::make('tournament_id')
                    ->label('Filter by Tournament')
                    ->relationship('tournament', 'name'),
                SelectFilter::make('game_title')
                    ->label('Filter by Game Title')
                    ->relationship('team.gameTitle', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'disqualified' => 'Disqualified',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (TournamentRegistration $record) => $record->status !== 'approved')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Approval Note / Confirmation Details')
                            ->placeholder('e.g. Roster verified, payment received.'),
                    ])
                    ->action(function (array $data, TournamentRegistration $record) {
                        $record->update([
                            'status' => 'approved',
                            'notes' => $data['notes'] ?? $record->notes,
                        ]);

                        if ($participant = $record->registeredBy) {
                            Notification::make()
                                ->success()
                                ->title('Registration Approved!')
                                ->body("Your team {$record->team?->name} has been approved for {$record->tournament?->name}.")
                                ->sendToDatabase($participant);

                            try {
                                Mail::to($participant->email)->send(new RegistrationStatusUpdatedMail($record, $data['notes'] ?? ''));
                            } catch (\Throwable $e) {
                            }
                        }

                        // Send Discord Webhook announcement
                        DiscordService::sendRegistrationNotification($record);

                        Notification::make()
                            ->success()
                            ->title('Registration approved & Discord webhook dispatched!')
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (TournamentRegistration $record) => $record->status !== 'rejected')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->placeholder('State why the registration is being rejected (e.g. invalid receipt).'),
                    ])
                    ->action(function (array $data, TournamentRegistration $record) {
                        $record->update([
                            'status' => 'rejected',
                            'notes' => $data['notes'],
                        ]);

                        if ($participant = $record->registeredBy) {
                            Notification::make()
                                ->danger()
                                ->title('Registration Rejected')
                                ->body("Your team {$record->team?->name} registration for {$record->tournament?->name} was rejected.")
                                ->sendToDatabase($participant);

                            try {
                                Mail::to($participant->email)->send(new RegistrationStatusUpdatedMail($record, $data['notes']));
                            } catch (\Throwable $e) {
                            }
                        }

                        // Send Discord Webhook announcement
                        DiscordService::sendRegistrationNotification($record);

                        Notification::make()
                            ->warning()
                            ->title('Registration rejected.')
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
