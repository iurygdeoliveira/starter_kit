<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Contabil;

use Filament\Pages\Page;

class LucroReal extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.contabil.lucro-real';

    #[\Override]
    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Contábil',
            url()->current() => 'Lucro Real',
        ];
    }
}
