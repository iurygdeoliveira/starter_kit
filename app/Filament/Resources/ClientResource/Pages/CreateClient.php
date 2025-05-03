<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Client criado com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success();
    }

    #[\Override]
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem (index) após criar o papel
        return ClientResource::getUrl('index');
    }
}
