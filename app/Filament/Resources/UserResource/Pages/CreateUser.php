<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = now();

        return $data;
    }

    #[\Override]
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem (index) após criar o usuário
        return UserResource::getUrl('index');
    }

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Funcionário criado com sucesso!')
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
            Action::make('voltar')
                ->label('Voltar')
                ->color('secondary')
                ->icon('heroicon-s-arrow-long-left')
                ->url(UserResource::getUrl('index')),
            $this->getCreateFormAction()->label('Criar Funcionário'),
            $this->getCreateAnotherFormAction()->label('Salvar e criar outro funcionário')
                ->color('primary')
                ->outlined(),
            $this->getCancelFormAction()
                ->color('danger')
                ->outlined(),
        ];
    }
}
