<?php

declare(strict_types = 1);

namespace App\Filament\Pages\CND;

use Filament\Pages\Page;

class cnd extends Page
{
    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Consultas';

    protected static string $view = 'filament.pages.cnd.cnd';

    protected static ?string $navigationLabel = 'CND';
}
