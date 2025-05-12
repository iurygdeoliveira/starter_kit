<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Filament\Resources\TaskResource;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    public $defaultAction = 'onboarding';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function onboardingAction(): Action
    {
        return Action::make('onboarding')
            ->requiresConfirmation()
            ->modalHeading('Cadastro de cliente necessário')
            ->modalDescription('Para cadastrar tarefas você precisa cadastrar um cliente primeiro.')
            ->modalIcon('heroicon-s-exclamation-triangle')
            ->color('warning')
            ->modalIconColor('warning')
            ->modalSubmitActionLabel('Cadastrar cliente')
            ->action(function (): void {
                $this->redirect(ClientResource::getUrl('create'));
            })
            ->visible(fn (): bool => Client::count() === 0);
    }
}
