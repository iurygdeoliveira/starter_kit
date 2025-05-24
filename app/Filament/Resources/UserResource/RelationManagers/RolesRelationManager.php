<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\Role as EnumRole;
use App\Models\Permission;
use App\Models\UserRolePermission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $title = 'Permissões';

    protected static ?string $icon = 'icon-permissoes';

    public array $pendingPermissionChanges = [];

    private array $originalPermissions = []; // Para comparar mudanças

    #[\Override]
    public function mount(): void
    {
        parent::mount();
        $this->loadCurrentPermissions();
    }

    #[\Override]
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        // Apenas verificar se o usuário já tem a role de Administração
        return ! $ownerRecord->hasRole(EnumRole::Administracao->value);
    }

    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('name')
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultPaginationPageOption(11)
            ->paginated([11, 'all'])
            ->emptyStateDescription('Uma vez que você defina funções de acesso, elas poderão ser configuradas aqui.')
            ->emptyStateIcon('heroicon-s-exclamation-triangle')
            ->columns([
                TextColumn::make('name')
                    ->label('Função')
                    ->sortable()
                    ->weight('bold'),
                ...$this->getPermissionToggleColumns(),
            ])
            ->filters([

            ])
            ->headerActions([
                Action::make('savePermissions')
                    ->label('Salvar Permissões')
                    ->color('success')
                    ->color(fn (): string => $this->getActionColor())
                    ->disabled(fn (): bool => $this->isActionDisabled())
                    ->action(fn () => $this->savePermissionChanges()),
            ])
            ->actions([

            ])
            ->bulkActions([

            ])
            ->defaultSort('name', 'asc');
    }

    /**
     * Carrega o estado atual das permissões do banco para o buffer
     */
    private function loadCurrentPermissions(): void
    {
        $userId      = $this->getOwnerRecord()->id;
        $permissions = Permission::all();
        $userRoles   = $this->getOwnerRecord()->roles;

        // Inicializa o buffer com o estado atual do banco
        foreach ($userRoles as $role) {
            foreach ($permissions as $permission) {
                $key = "{$role->id}_{$permission->id}";

                $hasPermission = UserRolePermission::where('user_id', $userId)
                    ->where('role_id', $role->id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                $this->pendingPermissionChanges[$key] = [
                    'user_id'       => $userId,
                    'role_id'       => $role->id,
                    'permission_id' => $permission->id,
                    'state'         => $hasPermission,
                ];

                // Armazena o estado original para comparação
                $this->originalPermissions[$key] = $hasPermission;
            }
        }
    }

    /**
     * Gera uma coluna toggle para cada permissão existente
     */
    public function getPermissionToggleColumns(): array
    {
        $columns = [];

        // Busca todas as permissões do banco de dados
        $permissions = Permission::all();

        // Itera sobre cada permissão do banco
        foreach ($permissions as $permission) {
            $columns[] = ToggleColumn::make("permissions.{$permission->id}")
                ->label($permission->name)
                ->onColor('success')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->alignCenter()
                ->getStateUsing(function ($record) use ($permission) {
                    // Sempre usa o estado do buffer pendingPermissionChanges
                    $key = "{$record->id}_{$permission->id}";

                    return $this->pendingPermissionChanges[$key]['state'] ?? false;
                })
                ->updateStateUsing(function ($record, $state) use ($permission) {
                    // Atualiza apenas o buffer, não o banco
                    $key = "{$record->id}_{$permission->id}";

                    $this->pendingPermissionChanges[$key] = [
                        'user_id'       => $this->getOwnerRecord()->id,
                        'role_id'       => $record->id,
                        'permission_id' => $permission->id,
                        'state'         => $state,
                    ];

                    return $state;
                });
        }

        return $columns;
    }

    /**
     * Salva todas as mudanças de permissões pendentes
     */
    public function savePermissionChanges(): void
    {
        foreach ($this->pendingPermissionChanges as $change) {
            if ($change['state']) {
                // Adicionar permissão: criar registro na tabela user_role_permissions
                UserRolePermission::firstOrCreate([
                    'user_id'       => $change['user_id'],
                    'role_id'       => $change['role_id'],
                    'permission_id' => $change['permission_id'],
                ]);
            } else {
                // Remover permissão: deletar registro da tabela user_role_permissions
                UserRolePermission::where('user_id', $change['user_id'])
                    ->where('role_id', $change['role_id'])
                    ->where('permission_id', $change['permission_id'])
                    ->delete();
            }
        }

        // Atualiza o estado original após salvar
        $this->updateOriginalPermissions();

        // Notificação de sucesso
        Notification::make()
            ->title('Permissões salvas com sucesso!')
            ->success()
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->send();
    }

    /**
    * Atualiza o estado original após salvar
    */
    private function updateOriginalPermissions(): void
    {
        foreach ($this->pendingPermissionChanges as $key => $change) {
            $this->originalPermissions[$key] = $change['state'];
        }
    }

    /**
     * Verifica se há mudanças em relação ao estado original
     */
    private function hasChanges(): bool
    {
        foreach ($this->pendingPermissionChanges as $key => $change) {
            $originalState = $this->originalPermissions[$key] ?? false;

            if ($change['state'] !== $originalState) {
                return true;
            }
        }

        return false;
    }

    /**
    * Determina se o botão deve estar desabilitado
    */
    private function isActionDisabled(): bool
    {
        // Desabilita se não há mudanças em relação ao estado original
        return ! $this->hasChanges();
    }

    /**
    * Determina a cor do botão baseado no estado
    */
    private function getActionColor(): string
    {
        // Se há mudanças pendentes, usar cor primária
        // Caso contrário, usar cor secundária
        return $this->hasChanges() ? 'primary' : 'secondary';
    }
}
