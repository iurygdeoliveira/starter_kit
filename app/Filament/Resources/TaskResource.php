<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Enums\Periodicity;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    // protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';

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
        return self::getCountTasks() > 0 ? (string) self::getCountTasks() : '!';
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
                Section::make('Dados da Tarefa')
                    ->icon('icon-tarefa')
                    ->description('Preencha os dados da tarefa a ser realizada para a empresa.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome da Tarefa :')
                            ->alpha()
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
                                    'Dashboard',
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
                        Select::make('periodicity')
                            ->label('Periodicidade :')
                            ->enum(Periodicity::class)
                            ->options([
                                Periodicity::Mensal->value     => 'Mensal',
                                Periodicity::Trimestral->value => 'Trimestral',
                                Periodicity::Anual->value      => 'Anual',
                            ])
                            ->required(),
                        //->native(),

                    ])->columns(2),
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
                TableAction::make('create')
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
                    ->sortable(),
                TextColumn::make('periodicity')
                    ->label('Periodicidade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
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

                SelectFilter::make('periodicity')
                    ->options([
                        Periodicity::Mensal->value     => 'Mensal',
                        Periodicity::Trimestral->value => 'Trimestral',
                        Periodicity::Anual->value      => 'Anual',
                    ])
                    ->label('Periodicidade'),
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
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
