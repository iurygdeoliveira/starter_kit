<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    // #[\Override]
    // protected function getCreatedNotification(): ?Notification
    // {
    //     return Notification::make()
    //         ->title('Usuário editado com sucesso!')
    //         ->color('success')
    //         ->icon('heroicon-s-check-circle')
    //         ->iconColor('success')
    //         ->seconds(8)
    //         ->success();
    // }
}
