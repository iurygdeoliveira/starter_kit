<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    // #[\Override]
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Adiciona o campo verified como false (não verificado) por padrão
    //     $data['verified'] = false;

    //     return $data;
    // }

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
