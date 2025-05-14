<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use App\Trait\SupportUserTrait;
use App\Trait\UserLoogedTrait;
use App\Trait\ValidateCpfTrait;
use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
use Illuminate\Support\Facades\Auth;

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
                                fn (): Closure => self::getCpfValidationRule(),
                            ])
                            ->validationMessages([
                                'unique' => 'Este CPF já está cadastrado no sistema.',
                            ])
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),
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
        \Filament\Forms\Components\Grid::make()
            ->schema([
                \Filament\Forms\Components\Toggle::make('is_admin')
                    ->label('Status de Administração')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->formatStateUsing(function ($record) {
                        if (!$record) return false;
                        return $record->roles->contains('name', 'Administração');
                    })
                    ->afterStateUpdated(function ($state, $record, \Filament\Forms\Set $set) {
                        if (!$record) return;
                        
                        // Find the admin role
                        $adminRole = \Spatie\Permission\Models\Role::where('name', 'Administração')->first();
                        
                        if (!$adminRole) {
                            Notification::make()
                                ->title('Erro ao alterar status de administração!')
                                ->body('Papel de Administração não encontrado no sistema.')
                                ->danger()
                                ->send();
                            return;
                        }
                        
                        // Get current roles
                        $currentRoles = $record->roles()->pluck('id')->toArray();
                        
                        if ($state) {
                            // Add admin role if it doesn't exist
                            if (!in_array($adminRole->id, $currentRoles)) {
                                $currentRoles[] = $adminRole->id;
                            }
                            $message = 'Administração habilitada com sucesso!';
                        } else {
                            // Remove admin role if exists
                            $currentRoles = array_diff($currentRoles, [$adminRole->id]);
                            $message = 'Administração desabilitada com sucesso!';
                        }
                        
                        // Sync roles
                        $record->roles()->sync($currentRoles);
                        
                        // Refresh the record
                        $record->refresh();
                        
                        // Send notification
                        Notification::make()
                            ->title($message)
                            ->color($state ? 'success' : 'danger')
                            ->icon('heroicon-s-check-circle')
                            ->iconColor($state ? 'success' : 'danger')
                            ->seconds(8)
                            ->success()
                            ->send();
                    })
                    ->live()
                    ->columnSpanFull(),
            ])
            ->columns(1),
                ]),
                    Section::make('Outras funções administrativas')
                    ->icon('heroicon-s-identification')
                    ->collapsible()
                    ->description('São as funções que o funcionário terá acesso no sistema')
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
                            }),
                    ])
                    ->schema([
                        Select::make('Funções')
                            ->multiple()
                            ->relationship('roles', 'name')
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
                        if ($record->roles->contains('name', 'Administração')) {
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
