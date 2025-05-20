<?php

// filepath: app/Filament/Resources/UserResource/Sections/OtherRolesSection.php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Sections;

use App\Enums\Role as EnumRole;
use App\Models\User;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class OtherRolesSection
{
    public static function make(): Section
    {
        return Section::make('Outras funções administrativas')
            ->icon('heroicon-s-identification')
            ->collapsible(fn ($livewire): bool => $livewire->record !== null)
            ->description('São as funções que o funcionário terá acesso no sistema')
            ->hidden(
                fn ($livewire, $get): bool => self::hideSectionCallback($livewire, $get)
            )
            ->headerActions([
                Action::make('Salvar Funções do Funcionário')
                    ->hidden(fn ($livewire): bool => self::hideActionCallback($livewire))
                    ->label('Salvar Funções')
                    ->action(fn ($livewire) => self::handleActionCallback($livewire)),
            ])
            ->schema([
                Select::make('Funções')
                    ->multiple()
                    ->relationship(
                        'roles',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->whereNotIn('name', [
                            EnumRole::Administracao->value,
                        ])
                    )
                    ->preload()
                    ->required()
                    ->columnSpan(2),
            ])
            ->columns(2);
    }

    /**
     * Determina se a seção deve ser ocultada
     */
    private static function hideSectionCallback($livewire, $get): bool
    {
        // Obter o usuário que está sendo editado
        $user = $livewire->record;

        if ($user) {
            // Para usuários existentes, esconder esta seção se:
            // 1. Já tiver a role de administração, OU
            // 2. O toggle is_admin estiver ativado (indicando que receberá a role)
            if ($user->hasRole(EnumRole::Administracao->value)) {
                return true;
            }

            return (bool)$get('is_admin');
        }

        // Para novos usuários, esconder com base no estado atual do toggle
        return (bool)$get('is_admin');
    }

    /**
     * Determina se o botão de ação deve ser ocultado
     */
    private static function hideActionCallback($livewire): bool
    {
        // Obter o usuário que está sendo editado
        $user = $livewire->record;

        // Se for criação de usuário (record é null), sempre esconder o botão
        return ! $user;
    }

    /**
    * Manipula a ação de salvar as funções do funcionário
    */
    private static function handleActionCallback($livewire): void
    {
        // Obter os dados do formulário
        $data = $livewire->form->getState();

        // Obter o registro atual
        $record = $livewire->record ?? new User();

        // Sincronizar as funções selecionadas com o usuário
        if (isset($data['Funções'])) {
            self::syncUserRoles($record, $data['Funções']);
        }

        // Notificar sucesso
        self::sendSuccessNotification();
    }

    /**
    * Sincroniza as funções selecionadas com o usuário
    */
    private static function syncUserRoles(User $user, array $roles): void
    {
        $user->roles()->sync($roles);
    }

    /**
     * Envia uma notificação de sucesso
     */
    private static function sendSuccessNotification(): void
    {
        Notification::make()
            ->title('Funções do funcionário atualizadas com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success()
            ->send();
    }
}
