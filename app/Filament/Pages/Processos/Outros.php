<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Processos;

use Filament\Pages\Page;

class Outros extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.processos.outros';

    #[\Override]
    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Processos',
            url()->current() => 'Outros Processos',
        ];
    }
}
