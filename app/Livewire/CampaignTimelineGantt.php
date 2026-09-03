<?php

namespace App\Livewire;

use App\Models\Campaign;
use Illuminate\Support\Carbon;
use Livewire\Component;

class CampaignTimelineGantt extends Component
{
    public string $viewRange = 'month'; // 'month' or 'quarter'

    public ?string $currentDate = null;

    public function mount(): void
    {
        $this->currentDate = now()->format('Y-m-d');
    }

    public function previousPeriod(): void
    {
        $date = Carbon::parse($this->currentDate);
        $this->currentDate = $this->viewRange === 'month'
            ? $date->subMonth()->format('Y-m-d')
            : $date->subMonths(3)->format('Y-m-d');
    }

    public function nextPeriod(): void
    {
        $date = Carbon::parse($this->currentDate);
        $this->currentDate = $this->viewRange === 'month'
            ? $date->addMonth()->format('Y-m-d')
            : $date->addMonths(3)->format('Y-m-d');
    }

    public function render()
    {
        $baseDate = Carbon::parse($this->currentDate);

        if ($this->viewRange === 'month') {
            $startDate = $baseDate->copy()->startOfMonth();
            $endDate = $baseDate->copy()->endOfMonth();
        } else {
            $startDate = $baseDate->copy()->startOfQuarter();
            $endDate = $baseDate->copy()->endOfQuarter();
        }

        $totalDays = $startDate->diffInDays($endDate) + 1;

        $campaigns = Campaign::query()
            ->with(['deliverables', 'owner'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($sub) use ($startDate, $endDate) {
                        $sub->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->orderBy('start_date', 'asc')
            ->get();

        return view('livewire.campaign-timeline-gantt', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalDays' => $totalDays,
            'campaigns' => $campaigns,
        ]);
    }
}
