<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Indicadores;

use App\Filament\Resources\TenantResource;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Pages\Page;

class Indicadores extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.indicadores.indicadores';

    public $defaultAction = 'onboarding';

    protected ?Tenant $cachedTenant = null;

    protected function getTenant(): ?Tenant
    {
        if (! $this->cachedTenant instanceof Tenant) {
            $this->cachedTenant = Tenant::first();
        }

        return $this->cachedTenant;
    }

    #[\Override]
    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Dashboard',
            url()->current() => 'Indicadores',
        ];
    }

    public function onboardingAction(): Action
    {
        return Action::make('onboarding')
            ->requiresConfirmation()
            ->modalHeading('Cadastro dos dados da empresa necessário')
            ->modalDescription('Para utilizar os indicadores você precisa cadastrar os dados da empresa primeiro.')
            ->modalIcon('heroicon-s-exclamation-triangle')
            ->color('warning')
            ->modalIconColor('warning')
            ->modalSubmitActionLabel('Cadastrar empresa')
            ->action(function () {
                $tenant = $this->getTenant() ?? Tenant::create();

                return $this->redirect(TenantResource::getUrl('edit', ['record' => $tenant->uuid]));
            })
            ->visible(function (): bool {
                $tenant = $this->getTenant();

                return ! $tenant instanceof Tenant || ! $tenant->cnpj || ! $tenant->phone || ! $tenant->email;
            });
    }
}
