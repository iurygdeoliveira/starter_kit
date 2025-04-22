<?php

declare(strict_types = 1);

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Empresa extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-m-building-office-2';

    protected static string $view = 'filament.pages.empresa';

    protected static ?string $navigationLabel = 'Minha Empresa';

    protected static ?string $title = 'Minha Empresa';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationBadgeTooltip = 'Preencher dados';

    // // Monta o formulário com os dados do tenant do usuário logado
    // public function mount(): void
    // {
    //     $this->form->fill($this->getFormModel()->toArray());
    // }

    // // Define qual registro será usado
    // protected function getFormModel(): Tenant
    // {
    //     return Auth::user()->tenant;
    // }

    public static function getNavigationBadge(): ?string
    {
        return '?';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // Campos do formulário (mesmos da migration)
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nome')
                ->required(),
            TextInput::make('cnpj')
                ->label('CNPJ')
                ->required(),
            TextInput::make('email')
                ->label('E‑mail')
                ->email()
                ->required(),
        ];
    }

    // Método acionado pelo botão “Salvar”
    public function submit(): void
    {
        $data = $this->form->getState();
        $this->getFormModel()->update($data);

        Notification::make()
            ->title('Dados salvos com sucesso')
            ->success()
            ->send();
    }
}
