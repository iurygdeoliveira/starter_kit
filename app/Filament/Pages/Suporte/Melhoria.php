<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Suporte;

use Filament\Pages\Page;

class Melhoria extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.suporte.melhoria';

    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Suporte',
            url()->current() => 'Melhoria',
        ];
    }
}
