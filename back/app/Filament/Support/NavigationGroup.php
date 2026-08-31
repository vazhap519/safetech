<?php

namespace App\Filament\Support;

use BackedEnum;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum NavigationGroup: string implements HasIcon, HasLabel
{
    case Services = 'სერვისები';
    case Projects = 'პროექტები';
    case Content = 'კონტენტი';
    case Sales = 'გაყიდვები';
    case Pages = 'გვერდები';
    case System = 'სისტემა';

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Services => 'heroicon-o-wrench-screwdriver',
            self::Projects => 'heroicon-o-briefcase',
            self::Content => 'heroicon-o-rectangle-stack',
            self::Sales => 'heroicon-o-inbox-arrow-down',
            self::Pages => 'heroicon-o-document-duplicate',
            self::System => 'heroicon-o-cog-6-tooth',
        };
    }
}
