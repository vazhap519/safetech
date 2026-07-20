<?php

namespace App\Filament\Support;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasLabel
{
    case Services = 'სერვისები';
    case Projects = 'პროექტები';
    case Blog = 'ბლოგი';
    case Content = 'კონტენტი';
    case Sales = 'გაყიდვები';
    case Pages = 'გვერდები';
    case System = 'სისტემა';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
