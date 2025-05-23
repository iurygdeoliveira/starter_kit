<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\Role as EnumRole;
use App\Models\Permission;
use App\Models\UserRolePermission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $title = 'Permissões de cada Função';

    protected static ?string $icon = 'icon-permissoes';

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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
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
                    // Verifica se existe uma associação user + role + permission na tabela user_role_permissions
                    $userId = $this->getOwnerRecord()->id; // Obtém o ID do usuário sendo editado

                    return UserRolePermission::where('user_id', $userId)
                        ->where('role_id', $record->id) // ID da role atual na linha
                        ->where('permission_id', $permission->id)
                        ->exists();
                })
                ->updateStateUsing(function ($record, $state) use ($permission) {
                    $userId = $this->getOwnerRecord()->id; // Obtém o ID do usuário sendo editado

                    if ($state) {
                        // Adicionar permissão: criar registro na tabela user_role_permissions
                        UserRolePermission::firstOrCreate([
                            'user_id'       => $userId,
                            'role_id'       => $record->id,
                            'permission_id' => $permission->id,
                        ]);
                    } else {
                        // Remover permissão: deletar registro da tabela user_role_permissions
                        UserRolePermission::where('user_id', $userId)
                            ->where('role_id', $record->id)
                            ->where('permission_id', $permission->id)
                            ->delete();
                    }

                    return $state;
                });
        }

        return $columns;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultPaginationPageOption(11)
            ->paginated([11, 'all'])
            ->emptyStateDescription('Uma vez que você defina funções de acesso, elas poderão ser configuradas aqui.')
            ->emptyStateIcon('heroicon-s-exclamation-triangle')
           // ->modifyQueryUsing(fn (Builder $query) => $query->with('permissions'))
            ->columns([
                TextColumn::make('name')
                    ->label('Função')
                    ->sortable()
                    ->weight('bold'),
                // Adicionamos uma coluna para cada permissão existente
                ...$this->getPermissionToggleColumns(),
            ])
            ->filters([
                //S
            ])
            ->headerActions([
                //AttachAction::make()
                //    ->label('Adicionar Função')
                //    ->preloadRecordSelect(),
                // Action::make('saveChanges')
                // ->label('Salvar alterações')
                //     ->button()
                //     ->color('primary')
                //     ->action(fn (RelationManager $livewire) => $livewire->savePermissionChanges())
                //     ->disabled(fn (RelationManager $livewire) => empty($livewire->pendingPermissionChanges)),
            ])
            ->actions([

            ])
            ->bulkActions([

            ])
            ->defaultSort('name', 'asc');
    }
}
