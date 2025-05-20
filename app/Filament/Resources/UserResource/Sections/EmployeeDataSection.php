<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Sections;

use App\Models\User;
use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class EmployeeDataSection
{
    public static function make(): Section
    {
        return Section::make('Dados do Funcionário')
            ->icon('icon-dados-usuario')
            ->collapsible(fn ($livewire): bool => $livewire->record !== null)
            ->description('São os dados do funcionário que serão utilizados para o login no sistema')
            ->headerActions([
                Action::make('Salvar Dados do Funcionário')
                    ->hidden(function ($livewire) {
                        // Obter o usuário que está sendo editado
                        $user = $livewire->record;

                        // Se for criação de usuário (record é null), sempre esconder o botão
                        if (! $user) {
                            return true;
                        }
                    })
                    ->label('Salvar Dados')
                    ->action(function ($livewire): void {
                        // Obter os dados do formulário
                        $data = $livewire->form->getState();

                        // Obter o registro atual
                        $record = $livewire->record ?? new User();

                        // Extrair apenas os campos específicos
                        $userData = [
                            'name'  => $data['name'] ?? $record->name,
                            'email' => $data['email'] ?? $record->email,
                            'cpf'   => $data['cpf'] ?? $record->cpf,
                            'phone' => $data['phone'] ?? $record->phone,
                        ];

                        // Salvar os dados
                        $record->fill($userData);
                        $record->save();

                        // Notificar sucesso
                        Notification::make()
                            ->title('Dados de acesso do funcionário atualizado com sucesso!')
                            ->color('success')
                            ->icon('heroicon-s-check-circle')
                            ->iconColor('success')
                            ->seconds(8)
                            ->success()
                            ->send();
                    }),
            ])
            ->schema([
                TextInput::make('name')
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
                TextInput::make('cpf')
                    ->label('CPF')
                    ->mask('999.999.999-99')
                    ->required()
                    ->dehydrated()
                    ->extraInputAttributes(['inputmode' => 'numeric'])
                    ->unique('users', 'cpf', ignoreRecord: true)
                    ->rules([
                        fn (): Closure => \App\Filament\Resources\UserResource::getCpfValidationRule(),
                    ])
                    ->validationMessages([
                        'unique' => 'Este CPF já está cadastrado no sistema.',
                    ]),
                TextInput::make('phone')
                    ->label('Fone')
                    ->mask('(99) 99999-9999')
                    ->required()
                    ->unique('users', 'phone', ignoreRecord: true)
                    ->dehydrated()
                    ->validationMessages([
                        'unique' => 'Este telefone já está cadastrado no sistema.',
                    ])
                    ->extraInputAttributes(['inputmode' => 'numeric']),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    // Obrigatório apenas na criação (quando record é null)
                    ->required(fn ($livewire): bool => $livewire->record === null)
                    // Só desidrata (envia para o banco) quando tem valor
                    ->dehydrated(fn ($state) => filled($state))
                    // Hash de senha só quando preenchido
                    ->dehydrateStateUsing(
                        fn ($state) => filled($state) ? Hash::make($state) : null
                    )
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->requiredWith('password')
                    ->dehydrated(false),
            ])->columns(2);
    }
}
