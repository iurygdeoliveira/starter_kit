<?php

declare(strict_types = 1);

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // #[\Override]
    // protected function getCreatedNotification(): ?Notification
    // {
    //     return Notification::make()
    //         ->title('Função editada com sucesso!')
    //         ->color('success')
    //         ->icon('heroicon-s-check-circle')
    //         ->iconColor('success')
    //         ->seconds(8)
    //         ->success();
    // }
}
