<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MarketingPlatform: string implements HasColor, HasIcon, HasLabel
{
    case Instagram = 'instagram';
    case TikTok = 'tiktok';
    case YouTube = 'youtube';
    case Facebook = 'facebook';
    case LinkedIn = 'linkedin';
    case TwitterX = 'x';
    case Threads = 'threads';
    case GoogleAds = 'google_ads';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Instagram => 'Instagram',
            self::TikTok => 'TikTok',
            self::YouTube => 'YouTube',
            self::Facebook => 'Facebook',
            self::LinkedIn => 'LinkedIn',
            self::TwitterX => 'X / Twitter',
            self::Threads => 'Threads',
            self::GoogleAds => 'Google Ads',
            self::Other => 'Other Platform',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Instagram => 'danger',
            self::TikTok => 'gray',
            self::YouTube => 'danger',
            self::Facebook => 'info',
            self::LinkedIn => 'info',
            self::TwitterX => 'gray',
            self::Threads => 'gray',
            self::GoogleAds => 'warning',
            self::Other => 'secondary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Instagram => 'heroicon-m-camera',
            self::TikTok => 'heroicon-m-musical-note',
            self::YouTube => 'heroicon-m-play',
            self::Facebook => 'heroicon-m-user-group',
            self::LinkedIn => 'heroicon-m-briefcase',
            self::TwitterX => 'heroicon-m-hashtag',
            self::Threads => 'heroicon-m-at-symbol',
            self::GoogleAds => 'heroicon-m-sparkles',
            self::Other => 'heroicon-m-globe-alt',
        };
    }
}
