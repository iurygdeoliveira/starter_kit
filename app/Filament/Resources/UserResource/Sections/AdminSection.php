<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Sections;

use App\Enums\Role as EnumRole;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AdminSection
{
    public static function make(): Section
    {
        return Section::make('Administrar Sistema')
            ->icon('icon-administrar')
            ->collapsible(fn ($livewire): bool => $livewire->record !== null)
            ->description('Habilitar administração do sistema para o funcionario')
            ->hidden(fn ($livewire): bool => self::hideSectionCallback($livewire))
            ->schema([
                Grid::make()
                    ->schema([
                        Toggle::make('is_admin')
                            ->dehydrated(false)
                            ->label((fn ($state): string => $state ? 'Habilitado' : 'Desabilitado'))
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-c-check')
                            ->offIcon('heroicon-c-x-mark')
                            ->reactive()
                            ->helperText(fn ($state): string => $state
                                ? 'O usuário tem acesso a administração do sistema'
                                : 'O usuário não tem acesso a administração do sistema')
                            ->columnSpanFull()
                            ->afterStateHydrated(
                                fn ($livewire, $set): bool => self::afterToggleStateHydratedCallback($livewire, $set)
                            )
                            ->afterStateUpdated(function ($livewire, $state, $set): void {}),
                    ])
                    ->columns(1),
            ])
            ->headerActions([
                Action::make('Permissão de Administração')
                    ->hidden(fn ($livewire): bool => self::hideActionCallback($livewire))
                    ->label('Salvar Permissão')
                    ->disabled(
                        fn ($livewire, $get): bool => self::disableActionCallback($livewire, $get)
                    )
                    ->color(
                        fn ($livewire, $get): string => self::colorActionCallback($livewire, $get)
                    )
                    ->action(
                        fn ($livewire, $get) => self::handleActionCallback($livewire, $get)
                    ),
            ]);
    }

    private static function hideSectionCallback($livewire): bool
    {
        // Obter o usuário que está sendo editado
        $record = $livewire->record;

        // Se não tiver registro (criação), mostra normalmente
        if (! $record) {
            return false;
        }

        // Compara o ID do usuário logado com o do registro
        // Se for o mesmo usuário, esconde a seção
        return Auth::id() === $record->id;
    }

    private static function afterToggleStateHydratedCallback($livewire, $set)
    {
        // Obter o usuário que está sendo editado
        $user = $livewire->record;

        // Verificar se o usuário tem a role "Administração"
        $isAdmin = $user ? $user->hasRole(EnumRole::Administracao->value) : false;

        // Define explicitamente o estado do campo
        $set('is_admin', $isAdmin);

        return $isAdmin;
    }

    private static function hideActionCallback($livewire): bool
    {
        // Obter o usuário que está sendo editado
        $user = $livewire->record;

        // Se for criação de usuário (record é null), sempre esconder o botão
        return ! $user;
    }

    private static function disableActionCallback($livewire, $get): bool
    {
        // Obter o usuário que está sendo editado
        $user = $livewire->record;

        // Obter o valor atual do toggle is_admin
        $currentToggleValue = $get('is_admin');

        // Na criação de usuário, habilitar o botão apenas se o toggle estiver ativado
        if (! $user) {
            // Verificar se todos os campos obrigatórios estão preenchidos
            $name  = $get('name');
            $email = $get('email');
            $cpf   = $get('cpf');
            $phone = $get('phone');

            // Se algum campo obrigatório não estiver preenchido, desabilitar o botão
            if (empty($name) || empty($email) || empty($cpf) || empty($phone)) {
                return true;
            }

            // Se todos os campos estiverem preenchidos, habilitar apenas se o toggle estiver ativado
            return ! $currentToggleValue;
        }

        // Verificar se o usuário tem a role "Administração"
        $originalHasAdminRole = $user->hasRole(EnumRole::Administracao->value);

        // Habilitar o botão apenas se o valor do toggle for diferente do estado atual da role
        return $currentToggleValue === $originalHasAdminRole; // Retorna true (desabilitado) se forem iguais
    }

    private static function colorActionCallback($livewire, $get): string
    {
        $user = $livewire->record;

        // Obter o valor atual do toggle is_admin
        $currentToggleValue = $get('is_admin');

        // Na criação de usuário, usar cor primária se o toggle estiver ativado
        if (! $user) {
            return $currentToggleValue ? 'primary' : 'secondary';
        }

        $originalHasAdminRole = $user->hasRole(EnumRole::Administracao->value);

        // Verificar se o toggle foi alterado
        if ($currentToggleValue === $originalHasAdminRole) {
            return 'secondary';
        }

        // Se não houve alteração, usar cor secundária
        return 'primary';
    }

    private static function handleActionCallback($livewire, $get): void
    {
        // Obter o usuário que está sendo editado
        $user = $livewire->record;

        // Obter o valor atual do toggle is_admin
        $isAdminToggleValue = $get('is_admin');

        // Verificar se o usuário tem a role "Administração"
        $isAdmin = $user->hasRole(EnumRole::Administracao->value);

        $adminRole = Role::where('name', EnumRole::Administracao->value)->first();

        // Se estiver tentando remover a role de administração, verificar se não ficará sem administradores
        if (! $isAdminToggleValue && $isAdmin && self::isLastAdmin($adminRole)) {
            self::showLastAdminErrorNotification($livewire);

            return;
        }

        self::updateAdminRole($user, $adminRole, $isAdminToggleValue);
    }

    private static function isLastAdmin($adminRole): bool
    {
        // Contar quantos usuários têm a role de Administração
        $adminUsersCount = User::whereHas('roles', function ($query) use ($adminRole): void {
            $query->where('roles.id', $adminRole->id);
        })->count();

        // Se este for o único administrador, impedir a remoção
        return $adminUsersCount <= 1;
    }

    private static function showLastAdminErrorNotification($livewire): void
    {
        Notification::make()
            ->title('Operação não permitida!')
            ->body('É necessário ter pelo menos um usuário com permissão de administração no sistema. Adicione outro administrador antes de remover esta permissão.')
            ->color('danger')
            ->icon('heroicon-s-x-circle')
            ->iconColor('danger')
            ->seconds(8)
            ->send();

        // Obter o estado atual do formulário para preservar todos os valores
        $currentFormState = $livewire->form->getState();

        // Atualizar apenas o campo is_admin mantendo todos os outros valores
        $currentFormState['is_admin'] = true;

        // Recarregar o formulário com todos os valores originais + is_admin corrigido
        $livewire->form->fill($currentFormState);
    }

    private static function updateAdminRole($user, $adminRole, $isAdminToggleValue): void
    {
        if ($isAdminToggleValue) {
            // Se o toggle estiver ativado:
            // 1. Remove TODAS as roles do usuário
            // 2. Adiciona apenas a role de administração
            $user->roles()->sync([$adminRole->id]);
        } else {
            // Se o toggle estiver desativado, remove apenas a role de administração
            // As outras roles serão gerenciadas pelo OtherRolesSection
            $user->roles()->detach($adminRole->id);
        }

        // Recarregar a relação para garantir que os dados estejam atualizados
        $user->load('roles');

        // Notificar sucesso
        Notification::make()
            ->title('Permissão de administração atualizada com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success()
            ->send();
    }
}
