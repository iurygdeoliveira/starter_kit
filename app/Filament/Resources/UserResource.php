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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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

    /**
     * Define o modelo associado a este recurso.
     * Neste caso, o recurso gerencia o modelo User.
     */
    protected static ?string $model = User::class;

    /**
     * Define o ícone de navegação para este recurso no menu lateral.
     * Utiliza o ícone 'rectangle-stack' da biblioteca Heroicons.
     */
    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?string $navigationGroup = 'Administração';

    // Opcional: Definir a ordem do item no grupo
    protected static ?int $navigationSort = 3;

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
            return 'Adicionar usuários';
        }

        // Todos os campos estão preenchidos
        return null;
    }

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('User');
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
                Section::make('Informações Pessoais')
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
                            ]),
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

                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->confirmed(),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->requiredWith('password')
                            ->dehydrated(false),
                        Select::make('Funções:')
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
                    ->sortable()
                    ->formatStateUsing(
                        fn (string $state): string => $state !== '' ? $state : 'Nenhuma função atribuída'
                    ),
                TextColumn::make('verified')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => $state !== '' && $state !== '0' ? 'Sim' : 'Não'
                    )
                    ->color(fn (string $state): string => $state !== '' && $state !== '0' ? 'success' : 'danger')
                    ->sortable()
                    ->searchable(),
            ])

            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Funções'),
            ])
            ->actions([
                EditAction::make()->hidden(fn (): bool => self::isSupportUser()),
                DeleteAction::make(),
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
     * Atualmente não há relacionamentos configurados, mas podem ser adicionados conforme necessário.
     */
    #[\Override]
    public static function getRelations(): array
    {
        $currentUrl = request()->url();

        // Verificar se estamos na página de edição
        // evitar exibir a tabela de relacionamento na página de edição
        if (str_contains($currentUrl, '/edit')) {
            return [];
        }

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
