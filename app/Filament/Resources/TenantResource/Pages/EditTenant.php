<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Trait\SupportUserTrait;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTenant extends EditRecord
{
    use SupportUserTrait;

    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    #[\Override]
    protected function getSavedNotification(): ?Notification
    {
        // Retorne sua notificação personalizada
        return Notification::make()
            ->title('Dados da empresa atualizados com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success();
    }

    #[\Override]
    protected function getRedirectUrl(): ?string
    {
        // Redireciona para a página de visualização após salvar
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
