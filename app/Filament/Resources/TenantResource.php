<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use App\Trait\SupportUserTrait;
use App\Trait\UserLoogedTrait;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TenantResource extends Resource
{
    use SupportUserTrait;
    use UserLoogedTrait;

    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-m-building-office-2';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Minha Empresa';

    protected static ?string $title = 'Minha Empresa';

    protected static ?int $navigationSort = 1;

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
        // Se é o usuário de suporte, mostrar contagem de tenants
        if (static::isSupportUser()) {
            return (string) Tenant::count();
        }

        $tenant = self::getUserTenant();

        // Verificar campos incompletos
        if (
            is_null($tenant) || is_null($tenant->cnpj) || is_null($tenant->phone) ||
            is_null($tenant->email) || is_null($tenant->name) || is_null($tenant->crc)) {
            return '!';
        }

        // Todos os campos estão preenchidos
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (static::isSupportUser()) {
            return 'primary';
        }

        $tenant = self::getUserTenant();

        // Verificar campos incompletos
        if (
            is_null($tenant) || is_null($tenant->cnpj) || is_null($tenant->phone) ||
            is_null($tenant->email) || is_null($tenant->name) || is_null($tenant->crc)) {
            return 'danger';
        }

        // Todos os campos estão preenchidos
        return 'primary';
    }

    #[\Override]
    public static function getNavigationBadgeTooltip(): ?string
    {
        if (static::isSupportUser()) {
            return null;
        }

        $tenant = self::getUserTenant();

        // Verificar campos incompletos
        if (
            is_null($tenant) || is_null($tenant->cnpj) || is_null($tenant->phone) ||
            is_null($tenant->email) || is_null($tenant->name) || is_null($tenant->crc)) {
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
                    ->required()
                    ->afterStateHydrated(function ($component): void {
                        $component->state(Auth::user()->tenant->name);
                    })
                    ->dehydrated(),
                TextInput::make('cnpj')
                    ->label('CNPJ')
                    ->mask('99.999.999/9999-99')
                    ->placeholder('CNPJ não cadastrado')
                    ->dehydrated()
                    ->extraInputAttributes(['inputmode' => 'numeric'])
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->label('Fone')
                    ->mask('(99) 99999-9999')
                    ->dehydrated()
                    ->placeholder('Fone não cadastrado')
                    ->extraInputAttributes(['inputmode' => 'numeric'])
                    ->unique(ignoreRecord: true),
                TextInput::make('email')
                    ->email()
                    ->dehydrated()
                    ->unique(ignoreRecord: true),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Fone')
                    ->sortable()
                    ->searchable(),
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
        ];
    }
}
