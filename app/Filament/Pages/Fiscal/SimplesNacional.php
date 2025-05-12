<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Fiscal;

use Filament\Pages\Page;

class SimplesNacional extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.fiscal.simples-nacional';

    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Fiscal',
            url()->current() => 'Simples Nacional',
        ];
    }
}
