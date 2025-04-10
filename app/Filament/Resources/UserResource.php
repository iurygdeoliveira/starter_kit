<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Recurso do Filament para gerenciamento de usuários.
 * Esta classe permite listar, criar, editar e excluir usuários através da interface administrativa do Filament.
 * Fornece formulários, tabelas e relacionamentos para o modelo User.
 */
class UserResource extends Resource
{
    /**
     * Define o modelo associado a este recurso.
     * Neste caso, o recurso gerencia o modelo User.
     */
    protected static ?string $model = User::class;

    /**
     * Define o ícone de navegação para este recurso no menu lateral.
     * Utiliza o ícone 'rectangle-stack' da biblioteca Heroicons.
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('User');
    }

    /**
     * Define o formulário para criar e editar registros de usuários.
     * Este método configura os campos do formulário, suas validações e comportamentos.
     * Inclui campos para nome, email e senha, com regras apropriadas para cada um.
     */
    #[\Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->requiredWith('password')
                    ->dehydrated(false),
            ]);
    }

    /**
     * Define a tabela para exibição e gerenciamento de usuários.
     * Este método configura a tabela com paginação, colunas, filtros, ações individuais e em massa.
     * Inclui colunas para nome, email, status de verificação e timestamps, com opções de busca e ordenação.
     */
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
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('verified')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state) => $state ? 'Sim' : 'Não'
                    )
                    ->color(fn (string $state) => $state ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->searchable()
                    ->sortable(),

            ])

            ->defaultSort('name', 'asc')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
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
        return [
            //
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
