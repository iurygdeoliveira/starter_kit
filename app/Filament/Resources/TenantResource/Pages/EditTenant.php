<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        $isSupportUser = Auth::user()->email === 'suporte@elshamahtec.com.br';

        if ($isSupportUser) {
            return [];
        }

        return [
            Actions\DeleteAction::make(),
        ];
    }
}
