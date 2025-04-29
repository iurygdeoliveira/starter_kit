<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    #[\Override]
    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->visible(false); // Torna o botão Criar e Salvar outro invisível
    }

    // #[\Override]
    // protected function afterCreate(): void
    // {
    //     // Pega o usuário atualmente autenticado
    //     $user = Auth::user();

    //     if ($user) {
    //         // Atualiza o tenant_id do usuário com o ID do tenant recém-criado
    //         $user->tenant_id = $this->record->id;
    //         $user->save();

    //         // Notificação opcional de sucesso
    //         Notification::make()
    //             ->title('Empresa registrada com sucesso')
    //             ->color('success')
    //             ->icon('heroicon-s-check-circle')
    //             ->iconColor('success')
    //             ->seconds(8)
    //             ->success()
    //             ->send();
    //     }
    // }
}
