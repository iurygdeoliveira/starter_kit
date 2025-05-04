<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Auth;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Register extends BaseRegister
{
    #[\Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('empresa')
                    ->label('Empresa')
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

        DB::beginTransaction();

        try {
            $tenant = Tenant::create([
                'name' => $data['empresa'],
            ]);

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'tenant_id' => $tenant->id,
            ]);

            // Busca a role de Administração (ou cria se não existir)
            $adminRole = Role::firstOrCreate(['name' => RoleEnum::Administração->value]);

            // Atribui a role ao usuário
            $user->roles()->attach($adminRole->id);

            DB::commit();

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
        } catch (Exception $e) {
            // Se qualquer exceção ocorrer, faz o rollback explícito
            DB::rollBack();

            // Notifica o erro
            Notification::make()
                ->title('Erro ao criar usuário')
                ->body('Ocorreu um erro ao criar o usuário e a empresa. Por favor, tente novamente.')
                ->icon('heroicon-c-no-symbol')
                ->iconColor('danger')
                ->color('danger')
                ->persistent()
                ->send();

            throw $e; // Re-lança a exceção para ser tratada pelo framework
        }
    }
}
