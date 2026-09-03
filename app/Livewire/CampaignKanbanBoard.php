<?php

namespace App\Livewire;

use App\Enums\CampaignPriority;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use Filament\Notifications\Notification;
use Livewire\Component;

class CampaignKanbanBoard extends Component
{
    public string $search = '';

    public string $priorityFilter = '';

    public function updateCampaignStatus(int $campaignId, string $newStatus): void
    {
        $campaign = Campaign::find($campaignId);
        $statusEnum = CampaignStatus::tryFrom($newStatus);

        if ($campaign && $statusEnum) {
            $oldLabel = $campaign->status?->getLabel() ?? 'Unknown';
            $campaign->update(['status' => $statusEnum]);

            Notification::make()
                ->title('Campaign Status Updated')
                ->body("{$campaign->title} moved from {$oldLabel} to {$statusEnum->getLabel()}.")
                ->success()
                ->send();
        }
    }

    public function render()
    {
        $statuses = CampaignStatus::cases();

        $campaignsQuery = Campaign::query()
            ->with(['owner', 'teamMembers', 'deliverables'])
            ->when($this->search, fn ($q) => $q->where(function ($sub) {
                $sub->where('title', 'like', "%{$this->search}%")
                    ->orWhere('campaign_code', 'like', "%{$this->search}%");
            }))
            ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
            ->orderBy('created_at', 'desc');

        $campaigns = $campaignsQuery->get();

        $columns = [];
        foreach ($statuses as $status) {
            $columns[$status->value] = [
                'status' => $status,
                'items' => $campaigns->where('status', $status),
            ];
        }

        return view('livewire.campaign-kanban-board', [
            'columns' => $columns,
            'priorities' => CampaignPriority::cases(),
        ]);
    }
}
