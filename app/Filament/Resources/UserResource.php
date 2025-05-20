<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Enums\Role as EnumRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Filament\Resources\UserResource\Sections\AdminSection;
use App\Filament\Resources\UserResource\Sections\EmployeeDataSection;
use App\Filament\Resources\UserResource\Sections\OtherRolesSection;
use App\Models\Role;
use App\Models\User;
use App\Trait\SupportUserTrait;
use App\Trait\UserLoogedTrait;
use App\Trait\ValidateCpfTrait;
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
                EmployeeDataSection::make(),
                AdminSection::make(),
                OtherRolesSection::make(),
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
                    ->tooltip('Excluir')
                    ->successNotification(null)
                    ->after(function (): void {
                        Notification::make()
                            ->title('Usuário excluído com sucesso')
                            ->success()
                            ->icon('heroicon-s-check-circle')
                            ->iconColor('success')
                            ->seconds(8)
                            ->send();
                    })
                    ->hidden(fn (User $record): bool => $record->id === Auth::id()), // Oculta para o próprio usuário

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(null)
                        ->after(function (): void {
                            Notification::make()
                                ->title('Usuários excluídos com sucesso')
                                ->success()
                                ->icon('heroicon-s-check-circle')
                                ->iconColor('success')
                                ->seconds(8)
                                ->send();
                        }),
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
