<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Fiscal;

use Filament\Pages\Page;

class LucroPresumido extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.fiscal.lucro-presumido';

    protected static ?string $navigationGroup = 'Fiscal';
}
