<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    // Redireciona para a página de listagem (index) após editar o usuário
    #[\Override]
    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }

    #[\Override]
    protected function getSavedNotification(): ?Notification
    {
        // Retorne sua notificação personalizada
        return Notification::make()
            ->title('Usuário atualizado com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success();
    }

    #[\Override]
    protected function getFormActions(): array
    {
        parent::getFormActions();

        return [
            // $this->getSaveFormAction(),
            // Action::make('voltar')
            //     ->label('Voltar')
            //     ->color('secondary')
            //     ->icon('heroicon-o-arrow-left')
            //     ->url(UserResource::getUrl('index')),
            // $this->getCancelFormAction(),

        ];
    }
}
