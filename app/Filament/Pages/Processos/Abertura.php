<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Processos;

use Filament\Pages\Page;

class Abertura extends Page
{
    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Processos';

    protected static string $view = 'filament.pages.processos.abertura';
}
