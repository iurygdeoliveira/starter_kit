<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Trait\SupportUserTrait;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    use SupportUserTrait;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        // Verifica se o usuário atual é um usuário de suporte
        if (self::isSupportUser()) {
            return [];
        }

        return [
            Actions\CreateAction::make(),
        ];
    }
}
