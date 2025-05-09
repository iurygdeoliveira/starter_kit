<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //Adiciona o campo verified como false (não verificado) por padrão
        $data['password'] = Hash::make('password');

        return $data;
    }

    #[\Override]
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem (index) após criar o usuário
        return UserResource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Dispara o evento Registered que envia o email de verificação
        // Este é o mesmo evento que o Filament usa internamente
        $user = $this->record;
        // Usar a rota de verificação de email do Filament
        $notification      = new VerifyEmail();
        $notification->url = filament()->getVerifyEmailUrl($user);
        $user->notify($notification);

        Notification::make()
            ->title('Email de verificação foi enviado para o usuario!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success()
            ->send();
    }

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Usuário criado com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success();
    }
}
