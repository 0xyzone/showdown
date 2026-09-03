<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DeliverableApprovalStatus: string implements HasColor, HasIcon, HasLabel
{
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case NeedsRevisions = 'needs_revisions';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PendingReview => 'Pending Review',
            self::Approved => 'Approved',
            self::NeedsRevisions => 'Needs Revisions',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PendingReview => 'amber',
            self::Approved => 'success',
            self::NeedsRevisions => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PendingReview => 'heroicon-m-clock',
            self::Approved => 'heroicon-m-check-circle',
            self::NeedsRevisions => 'heroicon-m-arrow-path',
        };
    }
}
