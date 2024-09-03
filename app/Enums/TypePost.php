<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TypePost: string implements HasColor, HasIcon, HasLabel
{
    case ARTICLE = 'article';
    case NEWS = 'news';
    case TRICK = 'trick';

    public function getColor(): string
    {
        return match ($this) {
            self::ARTICLE => 'info',
            self::NEWS => 'warning',
            self::TRICK => 'success'
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ARTICLE => 'Article',
            self::NEWS => 'News',
            self::TRICK => 'trick'
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ARTICLE => 'heroicon-o-document',
            self::NEWS => 'heroicon-o-megaphone',
            self::TRICK => 'heroicon-o-sparkles',
        };
    }
}
