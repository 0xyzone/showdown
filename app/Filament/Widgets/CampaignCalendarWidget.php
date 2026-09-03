<?php

namespace App\Filament\Widgets;

use App\Enums\DeliverableApprovalStatus;
use App\Enums\DeliverableType;
use App\Enums\MarketingPlatform;
use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use App\Models\CampaignDeliverable;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CampaignCalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = CampaignDeliverable::class;

    /**
     * Resolve record by handling 'deliverable-{id}' or raw ID,
     * while gracefully handling non-deliverable items (like 'campaign-{id}').
     */
    protected function resolveRecord(int|string $key): Model
    {
        $rawId = is_string($key) && str_starts_with($key, 'deliverable-')
            ? (int) str_replace('deliverable-', '', $key)
            : (int) $key;

        $record = CampaignDeliverable::query()->find($rawId);

        if (! $record) {
            $record = new CampaignDeliverable;
        }

        return $record;
    }

    /**
     * Handle event clicks cleanly. If it's a campaign bar, redirect to view campaign.
     * If it's a deliverable, open modal inspection.
     */
    public function onEventClick(array $event): void
    {
        $eventId = (string) ($event['id'] ?? '');

        if (str_starts_with($eventId, 'campaign-')) {
            $campaignId = (int) str_replace('campaign-', '', $eventId);
            $this->redirect(CampaignResource::getUrl('view', ['record' => $campaignId]));

            return;
        }

        parent::onEventClick($event);
    }

    /**
     * Fetch events for the FullCalendar view.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        $events = [];

        // 1. Deliverables scheduled in date window
        $deliverables = CampaignDeliverable::query()
            ->with(['campaign', 'assignee'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', $fetchInfo['start'])
            ->where('scheduled_at', '<=', $fetchInfo['end'])
            ->get();

        foreach ($deliverables as $item) {
            $statusColor = match ($item->approval_status) {
                DeliverableApprovalStatus::Approved => '#10b981', // emerald
                DeliverableApprovalStatus::PendingReview => '#f59e0b', // amber
                DeliverableApprovalStatus::NeedsRevisions => '#ef4444', // red
                default => '#8b5cf6', // purple
            };

            $platformName = $item->platform?->getLabel() ?? 'Social';
            $timeStr = $item->scheduled_at ? $item->scheduled_at->format('h:i A') : '';

            $events[] = [
                'id' => 'deliverable-'.$item->id,
                'title' => "{$timeStr} • [{$platformName}] {$item->title}",
                'start' => $item->scheduled_at->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => $statusColor,
                'borderColor' => $statusColor,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'deliverable',
                    'campaign_title' => $item->campaign?->title ?? 'N/A',
                    'campaign_id' => $item->campaign_id,
                    'platform' => $platformName,
                    'creative_type' => $item->creative_type?->getLabel() ?? 'Creative',
                    'status' => $item->approval_status?->getLabel() ?? 'Pending',
                    'assignee' => $item->assignee?->name ?? 'Unassigned',
                ],
            ];
        }

        // 2. Campaign duration boundaries
        $campaigns = Campaign::query()
            ->where('start_date', '<=', $fetchInfo['end'])
            ->where('end_date', '>=', $fetchInfo['start'])
            ->get();

        foreach ($campaigns as $camp) {
            $events[] = [
                'id' => 'campaign-'.$camp->id,
                'title' => "🎯 {$camp->title} ({$camp->campaign_code})",
                'start' => $camp->start_date->toDateString(),
                'end' => $camp->end_date->copy()->addDay()->toDateString(), // FullCalendar end date is exclusive
                'allDay' => true,
                'backgroundColor' => '#2563eb',
                'borderColor' => '#1d4ed8',
                'textColor' => '#ffffff',
                'url' => CampaignResource::getUrl('view', ['record' => $camp->id]),
                'extendedProps' => [
                    'type' => 'campaign',
                    'campaign_id' => $camp->id,
                    'status' => $camp->status?->getLabel() ?? '',
                    'budget' => 'Rs. '.number_format($camp->budget, 2),
                ],
            ];
        }

        return $events;
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Deliverable Details')
                ->icon('heroicon-o-document-text')
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Grid::make(3)->schema([
                        Select::make('creative_type')
                            ->label('Creative Type')
                            ->options(DeliverableType::class)
                            ->required(),

                        Select::make('platform')
                            ->label('Target Platform')
                            ->options(MarketingPlatform::class)
                            ->required(),

                        Select::make('approval_status')
                            ->label('Approval Status')
                            ->options(DeliverableApprovalStatus::class)
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Scheduled Publish Time')
                            ->native(false)
                            ->required(),

                        Select::make('assigned_to')
                            ->label('Assigned Creator')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ]),

                    TextInput::make('spend')
                        ->label('Spend / Budget (NPR)')
                        ->numeric()
                        ->prefix('Rs.'),

                    Textarea::make('copy_text')
                        ->label('Copy / Captions')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('designer_notes')
                        ->label('Production Notes')
                        ->rows(2)
                        ->columnSpanFull(),

                    FileUpload::make('asset_files')
                        ->label('Assets & Attachments')
                        ->multiple()
                        ->directory('campaign-deliverables')
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
            Action::make('viewCampaign')
                ->label('Open Campaign')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(fn (): ?string => $this->getRecord()?->campaign_id
                    ? CampaignResource::getUrl('view', ['record' => $this->getRecord()->campaign_id])
                    : null
                ),
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make()
            ->modalHeading(fn (): string => $this->getRecord()?->title ?? 'Deliverable Preview')
            ->modalWidth('3xl');
    }

    protected function headerActions(): array
    {
        return [];
    }

    public function config(): array
    {
        return [
            'initialView' => 'dayGridMonth',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
            ],
            'buttonText' => [
                'today' => 'Today',
                'dayGridMonth' => 'Month View',
                'timeGridWeek' => 'Week View',
                'timeGridDay' => 'Day View',
                'listMonth' => 'Agenda / List',
            ],
            'views' => [
                'dayGridMonth' => [
                    'dayMaxEvents' => 3,
                ],
                'timeGridWeek' => [
                    'slotMinTime' => '07:00:00',
                    'slotMaxTime' => '22:00:00',
                ],
            ],
            'navLinks' => true,
            'editable' => false,
            'eventTimeFormat' => [
                'hour' => 'numeric',
                'minute' => '2-digit',
                'meridiem' => 'short',
            ],
            'height' => 'auto',
            'nowIndicator' => true,
        ];
    }

    /**
     * Enhanced styling hook for calendar pills and typography.
     */
    public function eventClassNames(): string
    {
        return <<<'JS'
            function(arg) {
                let classes = ['cursor-pointer', 'shadow-xs', 'transition', 'hover:brightness-110', 'font-medium'];
                if (arg.event.allDay) {
                    classes.push('rounded-md', 'px-2', 'py-1', 'text-xs', 'font-semibold');
                } else {
                    classes.push('rounded-md', 'px-1.5', 'py-0.5', 'text-xs');
                }
                return classes;
            }
        JS;
    }
}
