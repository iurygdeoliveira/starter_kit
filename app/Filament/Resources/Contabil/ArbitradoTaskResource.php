<?php

declare(strict_types = 1);

namespace App\Filament\Resources\Contabil;

use App\Filament\Resources\Contabil\ArbitradoTaskResource\Pages;
use App\Models\Task;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArbitradoTaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationLabel = 'Lucro Arbitrado';

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListArbitradoTasks::route('/'),

        ];
    }
}
