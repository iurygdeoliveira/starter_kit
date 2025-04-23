<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-m-building-office-2';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Minha Empresa';

    protected static ?string $title = 'Minha Empresa';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationBadgeTooltip = 'Preencher dados';

    protected static bool $showTableBulkActions = false;

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Empresa');
    }

    public static function getNavigationBadge(): ?string
    {
        return '?';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    #[\Override]
    public static function getRecordRouteKeyName(): string
    {
        return 'uuid';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Razão Social')
                    ->required(),
                Forms\Components\TextInput::make('cnpj')
                    ->label('CNPJ')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Fone')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit'   => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
