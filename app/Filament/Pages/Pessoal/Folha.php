<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Pessoal;

use Filament\Pages\Page;

class Folha extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.pessoal.folha';

    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Pessoal',
            url()->current() => 'Folha de Pagamento',
        ];
    }
}
