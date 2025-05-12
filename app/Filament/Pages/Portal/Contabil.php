<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Portal;

use Filament\Pages\Page;

class Contabil extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.portal.contabil';

    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Portal do Cliente',
            url()->current() => 'Contábil',
        ];
    }
}
