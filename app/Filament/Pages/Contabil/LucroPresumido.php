<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Contabil;

use Filament\Pages\Page;

class LucroPresumido extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.contabil.lucro-presumido';

    protected static ?string $navigationGroup = 'Contábil';
}
