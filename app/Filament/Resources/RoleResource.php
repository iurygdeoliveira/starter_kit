<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use App\Trait\SupportUserTrait;
use App\Trait\UserLoogedTrait;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RoleResource extends Resource
{
    use SupportUserTrait;
    use UserLoogedTrait;

    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-s-identification';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?int $navigationSort = 4;

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Roles');
    }

    // #[\Override]
    // public static function shouldRegisterNavigation(): bool
    // {
    //     return false;
    // }

    #[\Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('name')
                    ->label('Nome da Função')
                    ->required()
                    ->unique(
                        'roles',
                        'name',
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule) {
                            return $rule->where('tenant_id', Auth::user()->tenant_id);
                        }
                    ),
            ])
            ->columns(2);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->extremePaginationLinks()
            ->defaultPaginationPageOption(20)
            ->paginated([20, 40, 60, 80, 'all'])
            ->emptyStateDescription('Uma vez que você cadastre sua primeira função, ela aparecerá aqui.')
            ->emptyStateIcon('heroicon-s-exclamation-triangle')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Registrar Função')
                    ->url(RoleResource::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome da Função'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->hidden(fn (): bool => self::isSupportUser()),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
