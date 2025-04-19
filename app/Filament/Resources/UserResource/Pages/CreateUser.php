<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Adiciona o campo verified como false (não verificado) por padrão
        $data['verified'] = false;

        return $data;
    }
}
