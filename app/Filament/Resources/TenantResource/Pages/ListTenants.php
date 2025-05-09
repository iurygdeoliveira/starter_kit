<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Models\Tenant;
use App\Trait\SupportUserTrait;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenants extends ListRecords
{
    use SupportUserTrait;

    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        if (static::isSupportUser()) {
            return [
                Actions\CreateAction::make(),
            ];
        }

        return [];
    }

    #[\Override]
    public function mount(): void
    {
        parent::mount();

        if (! static::isSupportUser()) {
            $tenant = Tenant::first();

            $this->redirect(TenantResource::getUrl('view', ['record' => $tenant]));
        }
    }
}
