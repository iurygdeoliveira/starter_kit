<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Role as EnumRole;
use App\Filament\Resources\UserResource;
use App\Models\Role;
use App\Notifications\WelcomeUserNotification;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Enviar email de boas-vindas para o usuário recém-criado
        $this->record->notify(new WelcomeUserNotification());

        // Verifica se o toggle is_admin está habilitado
        if ($this->data['is_admin'] ?? false) {
            // Se estiver habilitado, atribui a role de Administração
            $adminRole = Role::where('name', EnumRole::Administracao->value)->first();

            if ($adminRole) {
                // Use sync() em vez de attach() para evitar duplicatas
                $this->record->roles()->sync([$adminRole->id]);

                // Envia notificação de sucesso para atribuição da role de administrador
                Notification::make()
                    ->title('Role de Administração atribuída com sucesso!')
                    ->body('O usuário ' . $this->record->name . ' agora tem permissões de administrador.')
                    ->color('success')
                    ->icon('heroicon-s-shield-check')
                    ->iconColor('success')
                    ->seconds(8)
                    ->send();
            }
        } elseif (isset($this->data['Funções']) && ! empty($this->data['Funções'])) {
            // Se is_admin estiver desabilitado, verifica se há roles selecionadas
            // Use sync() em vez de attach() para evitar duplicatas
            $this->record->roles()->sync($this->data['Funções']);
            // Obter os nomes das roles para exibir na notificação
            $roleNames = Role::whereIn('id', $this->data['Funções'])->pluck('name')->toArray();
            $rolesText = count($roleNames) > 1
                ? implode(', ', array_slice($roleNames, 0, -1)) . ' e ' . end($roleNames)
                : $roleNames[0];
            // Envia notificação de sucesso para atribuição das roles
            Notification::make()
                ->title('Funções atribuídas com sucesso!')
                ->body('O usuário ' . $this->record->name . ' agora tem acesso às funções: ' . $rolesText)
                ->color('success')
                ->icon('heroicon-s-identification')
                ->iconColor('success')
                ->seconds(8)
                ->send();
        }
    }

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password']          = bcrypt('password');
        $data['email_verified_at'] = now();

        return $data;
    }

    #[\Override]
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem (index) após criar o usuário
        return UserResource::getUrl('index');
    }

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Funcionário criado com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success();
    }

    #[\Override]
    protected function getFormActions(): array
    {
        parent::getFormActions();

        return [
            Action::make('voltar')
                ->label('Voltar')
                ->color('secondary')
                ->icon('heroicon-s-arrow-long-left')
                ->url(UserResource::getUrl('index')),
            $this->getCreateFormAction()->label('Criar Funcionário'),
            $this->getCreateAnotherFormAction()->label('Salvar e criar outro funcionário')
                ->color('primary')
                ->outlined(),
            $this->getCancelFormAction()
                ->color('danger')
                ->outlined(),
        ];
    }
}
