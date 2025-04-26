<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use App\Trait\SupportUserTrait;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentPtbrFormFields\Document;
use Leandrocfe\FilamentPtbrFormFields\PhoneNumber;

class TenantResource extends Resource
{
    use SupportUserTrait;

    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-m-building-office-2';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Minha Empresa';

    protected static ?string $title = 'Minha Empresa';

    protected static ?int $navigationSort = 1;

    protected static bool $showTableBulkActions = false;

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Empresa');
    }

    public static function getNavigationBadge(): ?string
    {
        // Se é o usuário de suporte, mostrar contagem de tenants
        if (static::isSupportUser()) {
            return (string) Tenant::count();
        }

        $tenant = Tenant::first();

        // Verificar campos incompletos
        if ($tenant->cnpj === null || $tenant->phone === null || $tenant->email === null) {
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

        $tenant = Tenant::first();

        // Verificar campos incompletos
        if ($tenant->cnpj === null || $tenant->phone === null || $tenant->email === null) {
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

        $tenant = Tenant::first();

        // Verificar campos incompletos
        if ($tenant->cnpj === null || $tenant->phone === null || $tenant->email === null) {
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
                Document::make('cnpj')
                    ->label('CNPJ')
                    ->mask('99.999.999/9999-99')
                    ->validation(true)
                    ->placeholder('CNPJ não cadastrado')
                    ->dehydrated()
                    ->extraInputAttributes(['inputmode' => 'numeric'])
                    ->unique(ignoreRecord: true),
                PhoneNumber::make('phone')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Fone')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
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
