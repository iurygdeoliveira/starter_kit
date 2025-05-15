<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use App\Enums\Role as EnumRole;
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


    // Adicione este método
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        // Mostrar o RelationManager apenas se o usuário NÃO tiver a role de Administração
        return !$ownerRecord->hasRole(EnumRole::Administracao->value);
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
    /**
     * Gera uma coluna toggle para cada permissão existente
     */
    public static function getPermissionToggleColumns(): array
    {
        // Cache para as permissões para evitar múltiplas consultas
        static $permissionColumns = null;

        if ($permissionColumns === null) {
            $permissions       = Permission::orderBy('name')->get();
            $permissionColumns = [];

            foreach ($permissions as $permission) {
                $permissionId   = $permission->id; // Store the ID in a variable
                $permissionName = $permission->name; // Store the name in a variable

                $permissionColumns[] = ToggleColumn::make("permission_{$permissionId}")
                    ->label($permissionName)
                    ->afterStateUpdated(function (RelationManager $livewire, Model $record, bool $state) use ($permissionId, $permissionName) {
                        // $record é a Role atual
                        if ($state) {
                            // Adiciona a permissão à role se o toggle for ativado
                            $record->permissions()->syncWithoutDetaching([$permissionId]);
                            $livewire->notify('success', "Permissão '{$permissionName}' adicionada à role '{$record->name}'");
                        } else {
                            // Remove a permissão da role se o toggle for desativado
                            $record->permissions()->detach($permissionId);
                            $livewire->notify('success', "Permissão '{$permissionName}' removida da role '{$record->name}'");
                        }

                        // Reload the record with permissions to avoid lazy loading
                        $record->load('permissions');
                    })
                    ->getStateUsing(function (Model $record) use ($permissionId) {
                        // Não acessa diretamente permissions() que causaria lazy loading
                        // Em vez disso, usa a coleção permissions já carregada pelo with()
                        $permissionIds = $record->getRelation('permissions')->pluck('id')->toArray();

                        return in_array($permissionId, $permissionIds);
                    })
                    ->alignCenter()
                    ->onColor('primary')
                    ->offColor('danger');
            }
        }

        return $permissionColumns;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultPaginationPageOption(11)
            ->paginated([11, 'all'])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('permissions'))
            ->columns([
                TextColumn::make('name')
                    ->label('Função')
                    ->sortable()
                    ->weight('bold'),
                // Adicionamos uma coluna para cada permissão existente
                ...self::getPermissionToggleColumns(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Adicionar Função')
                    ->preloadRecordSelect(),
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
