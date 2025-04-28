<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Trait\ValidateCpfTrait;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Hash;

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
                    ->unique()
                    ->rules([
                        fn (): Closure => self::getCpfValidationRule(),
                    ]),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique()
                    ->maxLength(255),
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
        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'cpf'       => $data['cpf'],
            'password'  => Hash::make($data['password']),
            'tenant_id' => null,
        ]);
    }
}
