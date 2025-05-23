<?php

// filepath: app/Filament/Resources/UserResource/Sections/OtherRolesSection.php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Sections;

use App\Enums\Role as EnumRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRolePermission;
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
                    ->action(fn ($livewire) => self::handleActionCallback($livewire))
                    ->after(function ($livewire): void {
                        // Disparar evento quando o estado das roles mudar
                        $livewire->dispatch('roles-changed', changed: true);
                    }),
            ])
            ->schema([
                Select::make('roles')
                    ->label('Funções')
                    ->multiple()
                    ->relationship(
                        'roles',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->whereNotIn('name', [
                            EnumRole::Administracao->value,
                        ])
                    )
                    ->preload()
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

        $currentToggleValue = $get('is_admin');

        if ($user) {
            // Para usuários existentes, esconder esta seção se:
            // 1. Já tiver a role de administração, OU
            // 2. O toggle is_admin estiver ativado (indicando que receberá a role)
            if ($user->hasRole(EnumRole::Administracao->value) || $currentToggleValue === true) {
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
        $data = $livewire->data;

        // Obter o registro atual
        $record = $livewire->record;

        // Sincronizar as funções selecionadas com o usuário
        self::syncUserRoles($record, $data['roles']);
        self::syncRolePermissions($record, $data['roles']);

        // Notificar sucesso
        self::sendSuccessNotification();
    }

    /**
    * Sincroniza as funções selecionadas com o usuário
    * Se roles estiver vazio, remove todas as roles
    * Se roles estiver preenchido, sincroniza com as roles fornecidas
    */
    private static function syncUserRoles(User $user, array $roles): void
    {
        if ($roles === []) {
            // Se roles estiver vazio, remove todas as roles (exceto Administração)
            $adminRole = Role::where('name', EnumRole::Administracao->value)->first();

            if ($adminRole && $user->hasRole(EnumRole::Administracao->value)) {
                // Mantém apenas a role de Administração
                $user->roles()->sync([$adminRole->id]);
            } else {
                // Remove todas as roles
                $user->roles()->sync([]);
            }
        } else {
            // Sincroniza com as roles fornecidas
            $user->roles()->sync($roles);
        }
    }

    /**
    * Sincroniza as permissões para as roles fornecidas
    * Se roles estiver vazio, remove todas as permissões
    * Se roles estiver preenchido, atribui todas as permissões para cada role
    */
    private static function syncRolePermissions(User $user, array $roles): void
    {
        if ($roles === []) {
            // Se roles estiver vazio, remove todas as permissões do usuário
            UserRolePermission::where('user_id', $user->id)->delete();
        } else {
            // Remove todas as permissões existentes para este usuário primeiro
            UserRolePermission::where('user_id', $user->id)->delete();

            // Busca todas as permissões disponíveis no banco
            $allPermissions = Permission::all();

            // Para cada role fornecida
            foreach ($roles as $roleId) {
                // Para cada permissão disponível
                foreach ($allPermissions as $permission) {
                    // Cria a combinação user + role + permission
                    UserRolePermission::create([
                        'user_id'       => $user->id,
                        'role_id'       => $roleId,
                        'permission_id' => $permission->id,
                    ]);
                }
            }
        }
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
