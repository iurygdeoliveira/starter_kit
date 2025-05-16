<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    #[\Override]
    protected function getFormActions(): array
    {
        parent::getFormActions();

        return [
            Action::make('voltar')
                ->label('Voltar')
                ->color('secondary')
                ->icon('heroicon-s-arrow-long-left')
                ->url(TaskResource::getUrl('index')),
            $this->getCreateFormAction()
                ->label('Criar Tarefa'),
            $this->getCreateAnotherFormAction()
                ->label('Salvar e criar outra tarefa')
                ->color('primary')
                ->outlined(),
            $this->getCancelFormAction()
                ->color('danger')
                ->outlined(),
        ];
    }

    #[\Override]
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem (index) após criar o usuário
        return TaskResource::getUrl('index');
    }

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Tarefa criada com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success();
    }
}
