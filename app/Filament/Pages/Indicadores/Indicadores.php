<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Indicadores;

use Filament\Pages\Page;

class Indicadores extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.indicadores.indicadores';

    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Dashboard',
            url()->current() => 'Indicadores',
        ];
    }
}
