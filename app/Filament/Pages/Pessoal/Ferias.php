<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Pessoal;

use Filament\Pages\Page;

class Ferias extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.pessoal.ferias';

    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Pessoal',
            url()->current() => 'Férias',
        ];
    }
}
