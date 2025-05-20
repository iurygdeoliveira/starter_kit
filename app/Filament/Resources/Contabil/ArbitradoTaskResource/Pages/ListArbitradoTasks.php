<?php

declare(strict_types = 1);

namespace App\Filament\Resources\Contabil\ArbitradoTaskResource\Pages;

use App\Filament\Resources\Contabil\ArbitradoTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArbitradoTasks extends ListRecords
{
    protected static string $resource = ArbitradoTaskResource::class;

    protected static ?string $title = 'Lucro Arbitrado';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    #[\Override]
    public function getBreadcrumbs(): array
    {
        static::getNavigationLabel();

        return [
            '/'              => 'Contábil',
            url()->current() => 'Tarefas do Lucro arbitrado',
        ];
    }
}
