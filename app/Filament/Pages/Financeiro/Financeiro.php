<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Financeiro;

use Filament\Pages\Page;

class Financeiro extends Page
{
    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Clientes';

    protected static string $view = 'filament.pages.financeiro.financeiro';
}
