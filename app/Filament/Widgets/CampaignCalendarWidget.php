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
     * Handle event clicks cleanly: opens modal inspection/preview for deliverable items
     * without navigating away to another view page.
     */
    public function onEventClick(array $event): void
    {
        $eventId = (string) ($event['id'] ?? '');

        if (str_starts_with($eventId, 'campaign-')) {
            $campaignId = (int) str_replace('campaign-', '', $eventId);
            // Look up the first deliverable of this campaign or open the campaign view
            $firstDeliverable = CampaignDeliverable::query()->where('campaign_id', $campaignId)->first();
            if ($firstDeliverable) {
                $this->record = $firstDeliverable;
                $this->mountAction('view');

                return;
            }
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
            $statusValue = $item->approval_status?->value ?? 'pending_review';
            $statusColor = match ($item->approval_status) {
                DeliverableApprovalStatus::Approved => '#10b981', // emerald-500
                DeliverableApprovalStatus::PendingReview => '#f59e0b', // amber-500
                DeliverableApprovalStatus::NeedsRevisions => '#f43f5e', // rose-500
                default => '#8b5cf6', // purple-500
            };

            $statusBg = match ($item->approval_status) {
                DeliverableApprovalStatus::Approved => '#ecfdf5', // emerald-50
                DeliverableApprovalStatus::PendingReview => '#fffbeb', // amber-50
                DeliverableApprovalStatus::NeedsRevisions => '#fff1f2', // rose-50
                default => '#f5f3ff', // purple-50
            };

            $statusText = match ($item->approval_status) {
                DeliverableApprovalStatus::Approved => '#065f46', // emerald-800
                DeliverableApprovalStatus::PendingReview => '#92400e', // amber-800
                DeliverableApprovalStatus::NeedsRevisions => '#9f1239', // rose-800
                default => '#5b21b6', // purple-800
            };

            $platformEnums = $item->getPlatformEnums();
            $platformNames = array_map(fn ($p) => $p->getLabel(), $platformEnums);
            $platformKeys = array_map(fn ($p) => $p->value, $platformEnums);
            $platformName = ! empty($platformNames) ? implode(', ', $platformNames) : 'Social';
            $platformKey = $platformKeys[0] ?? 'other';
            $timeStr = $item->scheduled_at ? $item->scheduled_at->format('g:i A') : '';
            $creativeLabel = $item->creative_type?->getLabel() ?? 'Asset';
            $creator = $item->assignee?->name ?? null;

            $events[] = [
                'id' => 'deliverable-'.$item->id,
                'title' => ($timeStr ? "{$timeStr} • " : '')."[{$platformName}] {$item->title}",
                'start' => $item->scheduled_at->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => $statusColor,
                'borderColor' => $statusColor,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'deliverable',
                    'deliverable_id' => $item->id,
                    'campaign_title' => $item->campaign?->title ?? 'N/A',
                    'campaign_id' => $item->campaign_id,
                    'raw_title' => $item->title,
                    'time_str' => $timeStr,
                    'platform' => $platformName,
                    'platform_key' => $platformKey,
                    'platforms' => $platformKeys,
                    'creative_type' => $creativeLabel,
                    'status' => $item->approval_status?->getLabel() ?? 'Pending',
                    'status_key' => $statusValue,
                    'status_color' => $statusColor,
                    'status_bg' => $statusBg,
                    'status_text' => $statusText,
                    'assignee' => $creator,
                    'spend' => $item->spend ? 'Rs. '.number_format((float) $item->spend, 0) : null,
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
                'backgroundColor' => '#3b82f6',
                'borderColor' => '#2563eb',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'campaign',
                    'campaign_id' => $camp->id,
                    'code' => $camp->campaign_code,
                    'status' => $camp->status?->getLabel() ?? '',
                    'budget' => 'Rs. '.number_format((float) $camp->budget, 0),
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

                        Select::make('platforms')
                            ->label('Target Platforms')
                            ->multiple()
                            ->options(MarketingPlatform::class)
                            ->preload()
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
                let classes = ['cursor-pointer', 'transition', 'duration-150', 'overflow-hidden'];
                if (arg.event.allDay) {
                    classes.push('modern-campaign-pill');
                } else {
                    classes.push('modern-deliverable-pill');
                }
                return classes;
            }
        JS;
    }

    /**
     * Render rich HTML content for event pills for readability and modern appearance.
     */
    public function eventContent(): string
    {
        return <<<'JS'
            function(arg) {
                const props = arg.event.extendedProps || {};

                // 1. Campaign duration banner pill
                if (props.type === 'campaign') {
                    const el = document.createElement('div');
                    el.className = 'flex items-center gap-1.5 px-2 py-1 text-xs font-semibold text-white leading-tight w-full truncate';
                    el.innerHTML = `
                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-white/20 text-[10px] shrink-0 font-mono">🎯</span>
                        <span class="truncate font-semibold">${arg.event.title.replace('🎯 ', '')}</span>
                        ${props.budget ? `<span class="ml-auto text-[10px] font-mono px-1.5 py-0.2 bg-black/20 rounded-md shrink-0 opacity-90 hidden sm:inline-block">${props.budget}</span>` : ''}
                    `;
                    return { domNodes: [el] };
                }

                // 2. Deliverable item pill
                const el = document.createElement('div');
                el.className = 'deliverable-pill-content flex flex-col gap-0.5 px-2 py-1.5 text-xs w-full leading-tight';

                const platformBadges = {
                    'instagram': { bg: '#fce7f3', text: '#be185d', icon: '📷', label: 'IG' },
                    'tiktok': { bg: '#f3f4f6', text: '#111827', icon: '🎵', label: 'TikTok' },
                    'youtube': { bg: '#fee2e2', text: '#b91c1c', icon: '▶', label: 'YouTube' },
                    'facebook': { bg: '#dbeafe', text: '#1d4ed8', icon: '👥', label: 'FB' },
                    'linkedin': { bg: '#e0e7ff', text: '#3730a3', icon: '💼', label: 'LinkedIn' },
                    'x': { bg: '#f3f4f6', text: '#111827', icon: '𝕏', label: 'X' },
                    'threads': { bg: '#f3f4f6', text: '#111827', icon: '@', label: 'Threads' },
                    'google_ads': { bg: '#fef3c7', text: '#b45309', icon: '✨', label: 'Ads' },
                    'other': { bg: '#f3f4f6', text: '#4b5563', icon: '🌐', label: 'Other' }
                };

                const keys = (props.platforms && props.platforms.length > 0) ? props.platforms : [props.platform_key || 'other'];
                const badgesHtml = keys.slice(0, 2).map(k => {
                    const p = platformBadges[k] || { bg: '#f3f4f6', text: '#4b5563', icon: '📢', label: k };
                    return `<span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.2 rounded-md shrink-0 flex items-center gap-0.5" style="background-color: ${p.bg}; color: ${p.text};">
                        <span>${p.icon}</span>
                        <span>${p.label}</span>
                    </span>`;
                }).join('') + (keys.length > 2 ? `<span class="text-[9px] font-bold px-1 py-0.2 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 shrink-0">+${keys.length - 2}</span>` : '');

                const timeStr = props.time_str || '';
                const titleStr = props.raw_title || arg.event.title;
                const statusDotColor = props.status_color || '#10b981';

                el.innerHTML = `
                    <div class="flex items-center gap-1.5 w-full flex-wrap sm:flex-nowrap">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: ${statusDotColor}; box-shadow: 0 0 0 1.5px rgba(255,255,255,0.8);"></span>
                        <div class="flex items-center gap-1 flex-wrap shrink-0">
                            ${badgesHtml}
                        </div>
                        ${timeStr ? `<span class="text-[10px] text-gray-500 dark:text-gray-400 font-mono shrink-0 ml-auto">${timeStr}</span>` : ''}
                    </div>
                    <div class="deliverable-title font-semibold text-gray-900 dark:text-gray-100 truncate w-full mt-1 text-[11px] leading-snug">
                        ${titleStr}
                    </div>
                    ${props.assignee ? `
                        <div class="flex items-center gap-1 text-[9px] text-gray-500 dark:text-gray-400 truncate opacity-90 mt-0.5">
                            <span class="truncate">👤 ${props.assignee}</span>
                            ${props.creative_type ? `<span class="text-gray-300 dark:text-gray-600">•</span><span class="truncate">${props.creative_type}</span>` : ''}
                        </div>
                    ` : ''}
                `;

                return { domNodes: [el] };
            }
        JS;
    }
}
