<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Enums\Activity;
use App\Enums\Regime;
use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use App\Trait\UserLoogedTrait;
use App\Trait\ValidateCnpjTrait;
use Closure;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    use ValidateCnpjTrait;
    use UserLoogedTrait;

    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'icon-clients';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?int $navigationSort = 2;

    protected static $countClients;

    // lembrar de limitar associação do cliente com apenas a role portal do cliente

    protected static function getCountClients(): ?int
    {
        // Se já temos o resultado em cache, retorna imediatamente
        if (self::$countClients !== null) {
            return self::$countClients;
        }

        // Para usuários normais, pega diretamente da relação
        self::$countClients = Client::count();

        return self::$countClients;
    }

    public static function getNavigationBadge(): ?string
    {
        return self::getCountClients() > 0 ? (string) self::getCountClients() : '0';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (self::getCountClients() === 0) {
            return 'danger';
        }

        // Todos os campos estão preenchidos
        return 'primary';
    }

    #[\Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        if (self::getCountClients() === 0) {
            return 'Adicionar clientes';
        }

        // Todos os campos estão preenchidos
        return null;
    }

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Clients');
    }

    #[\Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados da Empresa')
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('Razão Social não cadastrada')
                            ->label('Razão Social')
                            ->dehydrated()
                            ->required()
                            ->unique('clients', 'name')
                            ->maxLength(255)
                            ->validationMessages([
                                'maxLength' => 'O nome não pode ter mais de 255 caracteres.',
                                'unique'    => 'Este nome já está cadastrado no sistema.',
                                'required'  => 'O nome é obrigatório.',
                            ]),
                        TextInput::make('cnpj')
                            ->placeholder('CNPJ não cadastrado')
                            ->label('CNPJ')
                            ->dehydrated()
                            ->required()
                            ->mask('99.999.999/9999-99')
                            ->unique('clients', 'cnpj')
                            ->rules([
                                fn (): Closure => self::getCnpjValidationRule(),
                            ])
                            ->validationMessages([
                                'unique'   => 'Este CNPJ já está cadastrado no sistema.',
                                'required' => 'O CNPJ é obrigatório.',
                            ])
                            ->extraInputAttributes(['inputmode' => 'numeric']),
                        Select::make('activity')
                            ->label('Atividade')
                            ->required()
                            ->options(collect(Activity::cases())->pluck('value', 'value'))
                            ->validationMessages([
                                'required' => 'A atividade é obrigatória.',
                            ]),
                        Select::make('regime')
                            ->label('Regime')
                            ->required()
                            ->options(collect(Regime::cases())->pluck('value', 'value'))
                            ->validationMessages([
                                'required' => 'O regime é obrigatório.',
                            ]),
                    ])
                    ->columns(2),
                Section::make('Informações de Acesso')
                    ->schema([
                        TextInput::make('user')
                            ->label('Nome')
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
            ->emptyStateDescription('Uma vez que você cadastre seus clientes, eles aparecerão aqui.')
            ->emptyStateIcon('heroicon-s-exclamation-triangle')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Registrar Cliente')
                    ->url(ClientResource::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Razão Social')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('activity')
                    ->label('Atividade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('regime')
                    ->label('Regime')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('regime')
                    ->label('Regime')
                    ->options(collect(Regime::cases())->pluck('value', 'value'))
                    ->multiple(),

                SelectFilter::make('activity')
                    ->label('Atividade')
                    ->options(collect(Activity::cases())->pluck('value', 'value'))
                    ->multiple(),
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
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit'   => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
