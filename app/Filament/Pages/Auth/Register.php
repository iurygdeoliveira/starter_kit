<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Trait\ValidateCpfTrait;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Register extends BaseRegister
{
    use ValidateCpfTrait;

    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->rule('regex:/^[^\d]*$/')
                    ->validationMessages([
                        'regex' => 'O nome não pode conter números.',
                    ]),
                TextInput::make('cpf')
                    ->label('CPF')
                    ->mask('999.999.999-99')
                    ->required()
                    ->dehydrated()
                    ->extraInputAttributes(['inputmode' => 'numeric'])
                    ->unique('users', 'cpf')
                    ->rules([
                        fn (): Closure => self::getCpfValidationRule(),
                    ])
                    ->validationMessages([
                        'unique' => 'Este CPF já está cadastrado no sistema.',
                    ]),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique('users', 'email')
                    ->maxLength(255)
                    ->validationMessages([
                        'unique' => 'Este email já está cadastrado no sistema.',
                    ]),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->dehydrated()
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->requiredWith('password')
                    ->dehydrated(false),
            ]);
    }

    #[\Override]
    protected function handleRegistration(array $data): User
    {
        // Verifica manualmente se o CPF já existe
        if (User::where('cpf', $data['cpf'])->exists()) {
            // Usa o sistema de validação do Laravel para retornar o erro adequadamente
            // Envia notificação de sucesso
            Notification::make()
                ->title('CPF já registrado')
                ->body('Por favor, corrija o CPF informado para continuar.')
                ->icon('heroicon-c-no-symbol')
                ->iconColor('danger')
                ->color('danger')
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'cpf' => 'Este CPF já está em uso.',
            ]);
        }

        if (User::where('email', $data['email'])->exists()) {
            // Usa o sistema de validação do Laravel para retornar o erro adequadamente
            // Envia notificação de sucesso
            Notification::make()
                ->title('Email já registrado')
                ->body('Por favor, corrija o email informado para continuar.')
                ->icon('heroicon-c-no-symbol')
                ->iconColor('danger')
                ->color('danger')
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'email' => 'Este email já está em uso.',
            ]);
        }

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'cpf'       => $data['cpf'],
            'password'  => Hash::make($data['password']),
            'tenant_id' => null,
        ]);

        // Envia notificação de sucesso
        Notification::make()
            ->title('Novo usuário criado com sucesso')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success()
            ->send();

        return $user;
    }
}
