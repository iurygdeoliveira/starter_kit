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
        return [
            // Actions\CreateAction::make(),
        ];
    }

    #[\Override]
    public function mount(): void
    {
        parent::mount();

        $tenant = Tenant::first();

        // Direciona para a página de visualização do primeiro registro
        $this->redirect(TenantResource::getUrl('view', ['record' => $tenant]));
    }
}
