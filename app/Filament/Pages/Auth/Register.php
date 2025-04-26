<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Auth;

use App\Models\Tenant;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('Empresa')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->rule('regex:/^[^\d]*$/')
                    ->validationMessages([
                        'regex' => 'O nome não pode conter números.',
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
        $tenant = Tenant::create([
            'name' => $data['Empresa'],
        ]);

        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'tenant_id' => $tenant->id,
        ]);
    }
}
