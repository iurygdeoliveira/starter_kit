<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Contabil;

use Filament\Pages\Page;

class Mei extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.contabil.mei';

    #[\Override]
    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Contábil',
            url()->current() => 'MEI',
        ];
    }
}
