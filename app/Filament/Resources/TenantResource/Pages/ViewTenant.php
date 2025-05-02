<?php

declare(strict_types = 1);

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            // Caso seja necessário enviar uma notificação de sucesso ao abrir a página de edição
            // ->successNotification(
            //     Notification::make()
            //         ->title('Dados da empresa atualizados com sucesso!')
            //         ->color('success')
            //         ->icon('heroicon-s-check-circle')
            //         ->iconColor('success')
            //         ->seconds(8)
            //         ->success()
            //         ->send()
            // ),
        ];
    }

    #[\Override]
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make([
                    TextEntry::make('name')
                        ->label('Razão Social')
                        ->placeholder(fn ($record): ?string => empty($record->name) ? 'Não cadastrado' : null),

                    TextEntry::make('email')
                        ->label('E-mail')

                        ->placeholder(fn ($record): ?string => empty($record->email) ? 'Email não cadastrado' : null),

                    TextEntry::make('cnpj')
                        ->label('CNPJ')
                        ->placeholder(fn ($record): ?string => empty($record->cnpj) ? 'CNPJ não cadastrado' : null),

                    TextEntry::make('phone')
                        ->label('Telefone')
                        ->placeholder(fn ($record): ?string => empty($record->phone) ? 'Telefone não cadastrado' : null),

                ])->columnSpan(2),
                Section::make([
                    TextEntry::make('created_at')
                        ->label('Criado em')
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label('Última Atualização')
                        ->dateTime(),
                ])->columnSpan(1),
            ])->columns(3);
    }
}
