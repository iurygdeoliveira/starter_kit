<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\RolesRelationManager;
use App\Models\Client;
use App\Models\Task;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?int $navigationSort = 4;

    protected static $countTasks;

    // lembrar de limitar associação do cliente com apenas a role portal do cliente

    protected static function getCountTasks(): ?int
    {
        // Se já temos o resultado em cache, retorna imediatamente
        if (self::$countTasks !== null) {
            return self::$countTasks;
        }

        // Para usuários normais, pega diretamente da relação
        self::$countTasks = Task::count();

        return self::$countTasks;
    }

    public static function getNavigationBadge(): ?string
    {
        return self::getCountTasks() > 0 ? (string) self::getCountTasks() : '0';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (self::getCountTasks() === 0) {
            return 'danger';
        }

        // Todos os campos estão preenchidos
        return 'primary';
    }

    #[\Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        if (self::getCountTasks() === 0) {
            return 'Adicionar tarefas';
        }

        // Todos os campos estão preenchidos
        return null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Client::count() > 0 ? true : false;
    }

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Tasks');
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

                TextInput::make('name')
                    ->label('Nome da Tarefa :')
                    ->required()
                    ->rules(['regex:/^[\pL\s\-\'\.]+$/u'])
                    ->validationMessages([
                        'regex' => 'O nome deve conter apenas letras, espaços e caracteres especiais (como acentos ou hífens).',
                    ]),
                // Campo de relacionamento com Role
                Select::make('role_id')
                    ->label('Pertence a Função :')
                    ->relationship(
                        'role',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->whereNotIn('name', [
                            'Administração',
                            'Cliente',
                            'Certidão Negativa de Débito (CND)',
                            'Suporte',
                            'Painel de Controle',
                            'Portal do Cliente',
                            'Financeiro',
                        ])
                    )
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('client_id')
                    ->label('Pertence ao Cliente :')
                    ->relationship('client', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->extremePaginationLinks()
            ->defaultPaginationPageOption(20)
            ->paginated([20, 40, 60, 80, 'all'])
            ->emptyStateDescription('Uma vez que você cadastre sua primeira tarefa, ela aparecerá aqui.')
            ->emptyStateIcon('heroicon-s-exclamation-triangle')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Registrar Tarefa')
                    ->url(TaskResource::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('role.name')
                    ->label('Função')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(
                        fn (string $state): string => $state !== '' ? $state : 'Nenhuma função atribuída'
                    ),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(
                        fn (string $state): string => $state !== '' ? $state : 'Nenhum cliente atribuído'
                    ),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Funções'),

                SelectFilter::make('client')
                    ->relationship('client', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Clientes'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        // $currentUrl = request()->url();

        // // Verificar se estamos na página de edição
        // // evitar exibir a tabela de relacionamento na página de edição
        // if (str_contains($currentUrl, '/edit')) {
        //     return [];
        // }

        return [
            // RolesRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit'   => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
