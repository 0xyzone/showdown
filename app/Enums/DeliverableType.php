<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DeliverableType: string implements HasColor, HasIcon, HasLabel
{
    case Reels = 'reels';
    case MotionGraphics = 'motion_graphics';
    case StaticGraphics2D = '2d_static_graphics';
    case Carousels = 'carousels';
    case Stories = 'stories';
    case Blogs = 'blogs';
    case AdCopies = 'ad_copies';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Reels => 'Reel / Short Video',
            self::MotionGraphics => 'Motion Graphics',
            self::StaticGraphics2D => '2D Static Graphic',
            self::Carousels => 'Carousel Post',
            self::Stories => 'Story',
            self::Blogs => 'Blog Article',
            self::AdCopies => 'Ad Copy / Text',
            self::Other => 'Other Asset',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Reels => 'danger',
            self::MotionGraphics => 'purple',
            self::StaticGraphics2D => 'info',
            self::Carousels => 'warning',
            self::Stories => 'amber',
            self::Blogs => 'success',
            self::AdCopies => 'cyan',
            self::Other => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Reels => 'heroicon-m-video-camera',
            self::MotionGraphics => 'heroicon-m-film',
            self::StaticGraphics2D => 'heroicon-m-photo',
            self::Carousels => 'heroicon-m-squares-2x2',
            self::Stories => 'heroicon-m-device-phone-mobile',
            self::Blogs => 'heroicon-m-newspaper',
            self::AdCopies => 'heroicon-m-pencil-square',
            self::Other => 'heroicon-m-document',
        };
    }
}
