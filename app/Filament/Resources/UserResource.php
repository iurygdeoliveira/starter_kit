<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Role as EnumRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\Role;
use App\Models\User;
use App\Trait\SupportUserTrait;
use App\Trait\UserLoogedTrait;
use App\Trait\ValidateCpfTrait;
use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Enum;

/**
 * Recurso do Filament para gerenciamento de usuários.
 * Esta classe permite listar, criar, editar e excluir usuários através da interface administrativa do Filament.
 * Fornece formulários, tabelas e relacionamentos para o modelo User.
 */
class UserResource extends Resource
{
    use SupportUserTrait;
    use UserLoogedTrait;
    use ValidateCpfTrait;

    protected static ?string $model = User::class;

    // protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static $countUsers;

    protected static function getCountUsers(): ?int
    {
        // Se já temos o resultado em cache, retorna imediatamente
        if (self::$countUsers !== null) {
            return self::$countUsers;
        }

        // Para usuários normais, pega diretamente da relação
        self::$countUsers = User::count();

        return self::$countUsers;
    }

    public static function getNavigationBadge(): ?string
    {
        return self::getCountUsers() > 0 ? (string) self::getCountUsers() : '0';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (self::getCountUsers() === 0) {
            return 'danger';
        }

        // Todos os campos estão preenchidos
        return 'primary';
    }

    #[\Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        if (self::getCountUsers() === 0) {
            return 'Adicionar Funcionarios';
        }

        // Todos os campos estão preenchidos
        return null;
    }

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Funcionário');
    }

    #[\Override]
    public static function getRecordRouteKeyName(): string
    {
        return 'uuid';
    }

    #[\Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados do Funcionário')
                    ->icon('icon-dados-usuario')
                    ->collapsible()
                    ->description('São os dados do funcionário que serão utilizados para o login no sistema')
                    ->headerActions([
                        Action::make('Salvar Dados do Funcionário')
                            ->label('Salvar Dados')
                            ->action(function ($livewire): void {
                                // Obter os dados do formulário
                                $data = $livewire->form->getState();

                                // Obter o registro atual
                                $record = $livewire->record ?? new User();

                                // Extrair apenas os campos específicos
                                $userData = [
                                    'name'  => $data['name'] ?? $record->name,
                                    'email' => $data['email'] ?? $record->email,
                                    'cpf'   => $data['cpf'] ?? $record->cpf,
                                    'phone' => $data['phone'] ?? $record->phone,
                                ];

                                // Salvar os dados
                                $record->fill($userData);
                                $record->save();

                                // Notificar sucesso
                                Notification::make()
                                    ->title('Dados de acesso do funcionário atualizado com sucesso!')
                                    ->color('success')
                                    ->icon('heroicon-s-check-circle')
                                    ->iconColor('success')
                                    ->seconds(8)
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->rules(['regex:/^[\pL\s\-\'\.]+$/u'])
                            ->validationMessages([
                                'regex' => 'O nome deve conter apenas letras, espaços e caracteres especiais (como acentos ou hífens).',
                            ]),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignoreRecord: true)
                            ->dehydrated()
                            ->validationMessages([
                                'unique' => 'Este e-mail já está cadastrado no sistema.',
                            ]),
                        TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->required()
                            ->dehydrated()
                            ->extraInputAttributes(['inputmode' => 'numeric'])
                            ->unique('users', 'cpf', ignoreRecord: true)
                            ->rules([
                                fn(): Closure => self::getCpfValidationRule(),
                            ])
                            ->validationMessages([
                                'unique' => 'Este CPF já está cadastrado no sistema.',
                            ])
                            ->disabled(fn(string $operation): bool => $operation === 'edit'),
                        TextInput::make('phone')
                            ->label('Fone')
                            ->mask('(99) 99999-9999')
                            ->required()
                            ->unique('users', 'phone', ignoreRecord: true)
                            ->dehydrated()
                            ->validationMessages([
                                'unique' => 'Este telefone já está cadastrado no sistema.',
                            ])
                            ->extraInputAttributes(['inputmode' => 'numeric']),

                    ])->columns(2),
                Section::make('Administrar Sistema')
                    ->icon('icon-administrar')
                    ->collapsible()
                    ->description('Habilitar administração do sistema para o funcionario')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Toggle::make('is_admin')
                                    ->dehydrated(false)
                                    ->label((fn($state) => $state ? 'Habilitado' : 'Desabilitado'))
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->onIcon('heroicon-c-check')
                                    ->offIcon('heroicon-c-x-mark')
                                    ->reactive()
                                    ->helperText(fn($state) => $state
                                        ? 'O usuário tem acesso a administração do sistema'
                                        : 'O usuário não tem acesso a administração do sistema')
                                    ->columnSpanFull()
                                    ->afterStateHydrated(function ($state, $livewire, $set) {
                                        // Obter o usuário que está sendo editado
                                        $user = $livewire->record;

                                        // Verificar se o usuário tem a role "Administração"
                                        $isAdmin = $user ? $user->hasRole(EnumRole::Administracao->value) : false;

                                        // Define explicitamente o estado do campo
                                        $set('is_admin', $isAdmin);

                                        return $isAdmin;
                                    }),
                            ])
                            ->columns(1),
                    ])
                    ->headerActions([
                        Action::make('Permissão de Administração')
                            ->label('Salvar Permissão')
                            ->disabled(function ($livewire, $get) {
                                // Obter o usuário que está sendo editado
                                $user = $livewire->record;

                                // Obter o valor atual do toggle is_admin
                                $currentToggleValue = $get('is_admin');

                                // Verificar se o usuário tem a role "Administração"
                                $originalHasAdminRole = $user->hasRole(EnumRole::Administracao->value);

                                // Habilitar o botão apenas se o valor do toggle for diferente do estado atual da role
                                return $currentToggleValue === $originalHasAdminRole;
                            })
                            ->action(function ($livewire, $get): void {
                                // Obter o usuário que está sendo editado
                                $user = $livewire->record;

                                // Obter o valor atual do toggle is_admin
                                $isAdminToggleValue = $get('is_admin');

                                // Verificar se o usuário tem a role "Administração"
                                $isAdmin = $user->hasRole(EnumRole::Administracao->value);

                                $adminRole = Role::where('name', EnumRole::Administracao->value)->first();

                                // Se estiver tentando remover a role de administração, verificar se não ficará sem administradores
                                if (!$isAdminToggleValue && $isAdmin) {
                                    // Contar quantos usuários têm a role de Administração (incluindo este)
                                    $adminUsersCount = User::whereHas('roles', function ($query) use ($adminRole) {
                                        $query->where('roles.id', $adminRole->id);
                                    })->count();

                                    // Se este for o único administrador, impedir a remoção
                                    if ($adminUsersCount <= 1) {
                                        Notification::make()
                                            ->title('Operação não permitida!')
                                            ->body('É necessário ter pelo menos um usuário com permissão de administração no sistema. Adicione outro administrador antes de remover esta permissão.')
                                            ->color('danger')
                                            ->icon('heroicon-s-x-circle')
                                            ->iconColor('danger')
                                            ->seconds(8)
                                            ->send();

                                        // Redefina o toggle para refletir o estado real da permissão
                                        $livewire->form->fill(['is_admin' => true]);

                                        // Encerra a execução da função
                                        return;
                                    }
                                }

                                if ($isAdminToggleValue) {
                                    // Se o toggle estiver ativado, adiciona a role se não existir
                                    // Usando syncWithoutDetaching para evitar duplicatas
                                    $user->roles()->syncWithoutDetaching([$adminRole->id]);
                                } else {
                                    // Se o toggle estiver desativado, remove a role
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
                            }),
                    ]),
                Section::make('Outras funções administrativas')
                    ->icon('heroicon-s-identification')
                    ->collapsible()
                    ->description('São as funções que o funcionário terá acesso no sistema')
                    ->hidden(function ($livewire) {
                        // Obter o usuário que está sendo editado
                        $user = $livewire->record;

                        // Esconder a seção se o usuário tiver a role de administração
                        return $user && $user->hasRole(EnumRole::Administracao->value);
                    })
                    ->headerActions([
                        Action::make('Salvar Funções do Funcionário')
                            ->label('Salvar Funções')
                            ->action(function ($livewire): void {
                                // Obter os dados do formulário
                                $data = $livewire->form->getState();

                                // Obter o registro atual
                                $record = $livewire->record ?? new User();

                                // Sincronizar as funções selecionadas com o usuário
                                if (isset($data['Funções'])) {
                                    $record->roles()->sync($data['Funções']);
                                }

                                // Notificar sucesso
                                Notification::make()
                                    ->title('Funções do funcionário atualizadas com sucesso!')
                                    ->color('success')
                                    ->icon('heroicon-s-check-circle')
                                    ->iconColor('success')
                                    ->seconds(8)
                                    ->success()
                                    ->send();
                            })
                    ])
                    ->schema([
                        Select::make('Funções')
                            ->multiple()
                            ->relationship(
                                'roles',
                                'name',
                                modifyQueryUsing: fn($query) => $query->whereNotIn('name', [
                                    EnumRole::Administracao->value,
                                ])
                            )
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(2),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->extremePaginationLinks()
            ->defaultPaginationPageOption(20)
            ->paginated([20, 40, 60, 80, 'all'])
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Funções')
                    ->badge()
                    ->searchable()
                    ->placeholder('Funções não atribuídas')
                    ->sortable()
                    ->getStateUsing(function (User $record): array {
                        // Verifica se o usuário tem a role 'Administração'
                        if ($record->hasRole(EnumRole::Administracao->value)) {
                            // Se tiver, retorna apenas essa role como array com um único elemento
                            return ['Administração'];
                        }

                        // Caso contrário, retorna todas as roles normalmente
                        return $record->roles->pluck('name')->toArray();
                    }),
                TextColumn::make('email_verified_at')
                    ->label('Email Verificado')
                    ->sortable()
                    ->searchable()
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Usuário não verificado'),

            ])

            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Funções'),

                TernaryFilter::make('verificado')
                    ->label('Email Verificado')
                    ->attribute('email_verified_at')
                    ->trueLabel('Usuários Verificados')
                    ->falseLabel('Usários não verificados')
                    ->nullable(),
            ])
            ->actions([
                EditAction::make()
                    ->label('')
                    ->icon('heroicon-s-pencil-square') // Define o ícone
                    ->tooltip('Editar'), // Define o tooltip,,
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Excluir'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Define os relacionamentos disponíveis para o recurso de usuário.
     * Este método configura quais relacionamentos serão exibidos na interface do Filament.
     */
    #[\Override]
    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
        ];
    }

    /**
     * Define as páginas disponíveis para o recurso de usuário.
     * Este método configura as rotas e páginas que serão usadas para listar, criar e editar usuários.
     * Inclui páginas para listagem (index), criação (create) e edição (edit) de usuários, com suas respectivas rotas.
     */
    #[\Override]
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
