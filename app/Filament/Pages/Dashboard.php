<?php

declare(strict_types = 1);

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-home';

    protected static string $view = 'filament.pages.dashboard';

    #[\Override]
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-s-home';
    }
}
