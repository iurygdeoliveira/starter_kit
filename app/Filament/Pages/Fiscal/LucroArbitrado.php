<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Fiscal;

use Filament\Pages\Page;

class LucroArbitrado extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.fiscal.lucro-arbitrado';

    protected static ?string $navigationGroup = 'Fiscal';
}
