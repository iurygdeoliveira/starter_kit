<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    #[\Override]
    protected function getFormActions(): array
    {
        parent::getFormActions();

        return [
            Action::make('voltar')
                ->label('Voltar')
                ->color('secondary')
                ->icon('heroicon-s-arrow-long-left')
                ->url(ClientResource::getUrl('index')),
            $this->getCreateFormAction()->label('Criar Cliente'),
            $this->getCreateAnotherFormAction()
                ->label('Salvar e criar outro cliente')
                ->color('primary')
                ->outlined(),
            $this->getCancelFormAction()
                ->color('danger')
                ->outlined(),
        ];
    }

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //Adiciona o campo verified como false (não verificado) por padrão
        $data['password']          = Hash::make('password');
        $data['email_verified_at'] = now();

        return $data;
    }

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Cliente criado com sucesso!')
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
