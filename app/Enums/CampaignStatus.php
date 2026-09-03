<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CampaignStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case InPlanning = 'in_planning';
    case InProduction = 'in_production';
    case Scheduled = 'scheduled';
    case Running = 'running';
    case Review = 'review';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::InPlanning => 'In Planning',
            self::InProduction => 'In Production',
            self::Scheduled => 'Scheduled',
            self::Running => 'Running',
            self::Review => 'In Review',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::InPlanning => 'info',
            self::InProduction => 'warning',
            self::Scheduled => 'purple',
            self::Running => 'success',
            self::Review => 'amber',
            self::Completed => 'emerald',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-m-document',
            self::InPlanning => 'heroicon-m-clipboard-document-list',
            self::InProduction => 'heroicon-m-wrench-screwdriver',
            self::Scheduled => 'heroicon-m-clock',
            self::Running => 'heroicon-m-play-circle',
            self::Review => 'heroicon-m-eye',
            self::Completed => 'heroicon-m-check-badge',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }
}
