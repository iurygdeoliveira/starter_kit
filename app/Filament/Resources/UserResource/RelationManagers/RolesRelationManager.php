<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {

        $permissions = Permission::all();
        
        $columns = [
            TextColumn::make('name')
                ->label('Funções')
                ->sortable(),
        ];
        
        // Adiciona colunas dinâmicas de permissões com toggles
        foreach ($permissions as $permission) {
            $columns[] = ToggleColumn::make($permission->name)
                // ->label($permission->name)
                ->alignCenter();
                // ->afterStateUpdated(function (RelationManager $livewire, Model $record, $state) use ($permission) {
                //     if ($state) {
                //         $record->permissions()->attach($permission->id);
                //     } else {
                //         $record->permissions()->detach($permission->id);
                //     }
                // })
                // ->getStateUsing(function (Model $record) use ($permission): bool {
                //     return $record->permissions->contains('id', $permission->id);
                // });
        }
        
        return $table
            ->recordTitleAttribute('name')
            ->defaultPaginationPageOption(11)
            ->paginated([11, 'all'])
            ->columns($columns)
            ->filters([
                //
            ])
            ->headerActions([
                
            ])
            ->actions([
               
            ])
            ->bulkActions([
               
            ])
            ->defaultSort('name', 'asc');
    }
}
