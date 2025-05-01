<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    // Adicionando validação em tempo real
    protected function getEmailFormComponent(): Component
    {
        $component = parent::getEmailFormComponent();
        $component
            ->live(onBlur: true)
            ->afterStateUpdated(
                function (CustomLogin $livewire) {
                    $livewire->validateOnly('data.email');
                }
            );

        return $component;
    }
}
