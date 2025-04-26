<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Trait\SupportUserTrait;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenant extends EditRecord
{
    use SupportUserTrait;

    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        if (static::isSupportUser()) {
            return [
                Actions\DeleteAction::make(),
            ];
        }

        return [];
    }
}
