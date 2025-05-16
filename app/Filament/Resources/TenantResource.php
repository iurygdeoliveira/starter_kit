<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use App\Trait\SupportUserTrait;
use App\Trait\UserLoogedTrait;
use App\Trait\ValidateCnpjTrait;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    use SupportUserTrait;
    use UserLoogedTrait;
    use ValidateCnpjTrait;

    protected static ?string $model = Tenant::class;

    // protected static ?string $navigationIcon = 'heroicon-m-building-office-2';

    protected static ?string $navigationLabel = 'Minha Empresa';

    protected static bool $showTableBulkActions = false;

    // Armazena o resultado em uma propriedade estática
    protected static $cachedTenant;

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Empresa');
    }

    /**
    * Busca o tenant do usuário atual usando cache estático
    */
    protected static function getUserTenant(): ?Tenant
    {
        // Se já temos o resultado em cache, retorna imediatamente
        if (self::$cachedTenant !== null) {
            return self::$cachedTenant;
        }

        if (self::isUserLoggedIn()) {
            // Para usuários normais, pega diretamente da relação
            self::$cachedTenant = Tenant::first();
        }

        return self::$cachedTenant;
    }

    public static function getNavigationBadge(): ?string
    {
        $tenant = self::getUserTenant();

        // Verificar campos incompletos
        if (
            is_null($tenant) || is_null($tenant->cnpj) || is_null($tenant->phone) ||
            is_null($tenant->email)) {
            return '!';
        }

        // Todos os campos estão preenchidos
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $tenant = self::getUserTenant();

        // Verificar campos incompletos
        if (
            is_null($tenant) || is_null($tenant->cnpj) || is_null($tenant->phone) ||
            is_null($tenant->email)) {
            return 'danger';
        }

        // Todos os campos estão preenchidos
        return 'primary';
    }

    #[\Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        $tenant = self::getUserTenant();

        // Verificar campos incompletos
        if (
            is_null($tenant) || is_null($tenant->cnpj) || is_null($tenant->phone) ||
            is_null($tenant->email)) {
            return 'Preencher dados da empresa';
        }

        // Todos os campos estão preenchidos
        return null;
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
                    ->label('Razão Social')
                    ->dehydrated()
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'maxLength' => 'O nome não pode ter mais de 255 caracteres.',
                    ]),
                TextInput::make('cnpj')
                    ->placeholder('CNPJ não cadastrado')
                    ->label('CNPJ')
                    ->dehydrated()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->mask('99.999.999/9999-99')
                    ->unique('tenants', 'cnpj', ignoreRecord: true)
                    ->rules([
                        fn (): Closure => self::getCnpjValidationRule(),
                    ])
                    ->validationMessages([
                        'unique'   => 'Este CNPJ já está cadastrado no sistema.',
                        'required' => 'O CNPJ é obrigatório.',
                    ])
                    ->extraInputAttributes(['inputmode' => 'numeric']),
                TextInput::make('phone')
                    ->placeholder('Fone não cadastrado')
                    ->label('Fone')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated()
                    ->mask('(99) 99999-9999')
                    ->extraInputAttributes(['inputmode' => 'numeric'])
                    ->unique('tenants', 'phone', ignoreRecord: true)
                    ->validationMessages([
                        'unique'   => 'Este Telefone já está cadastrado no sistema.',
                        'required' => 'O telefone é obrigatório.',
                    ]),
                TextInput::make('email')
                    ->placeholder('Email não cadastrado')
                    ->email()
                    ->dehydrated()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->unique('tenants', 'email', ignoreRecord: true)
                    ->validationMessages([
                        'unique'   => 'Este email já está cadastrado no sistema.',
                        'required' => 'O email é obrigatório.',
                    ]),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->extremePaginationLinks()
            ->defaultPaginationPageOption(20)
            ->paginated([20, 40, 60, 80, 'all'])
            ->emptyStateDescription('Uma vez que você cadastre os dados de sua empresa, eles aparecerão aqui.')
            ->emptyStateIcon('heroicon-s-exclamation-triangle')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Registrar Empresa')
                    ->url(TenantResource::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Razão Social')
                    ->weight(FontWeight::Bold),
                TextColumn::make('cnpj')
                    ->label('CNPJ'),
                TextColumn::make('email'),
                TextColumn::make('phone')
                    ->label('Fone'),
            ])
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
            'index'  => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit'   => Pages\EditTenant::route('/{record}/edit'),
            'view'   => Pages\ViewTenant::route('/{record}'),
        ];
    }
}
