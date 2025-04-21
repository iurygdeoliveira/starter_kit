<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function beforeFill(): void
    {
        session([
            'tenant.cnpj' => Auth::user()->tenant->cnpj,
            'tenant.name' => Auth::user()->tenant->name,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
