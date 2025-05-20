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
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Enviar email de boas-vindas para o usuário recém-criado
        $this->record->notify(new WelcomeUserNotification());

        // Atribuir roles com base na configuração do formulário
        $this->assignRolesToUser();
    }

    /**
    * Atribui roles ao usuário com base na configuração do formulário
    */
    protected function assignRolesToUser(): void
    {
        if ($this->data['is_admin'] ?? false) {
            $this->assignAdminRole();
        } else {
            $this->assignOtherRoles();
        }
    }

    /**
     * Atribui a role de administrador ao usuário
     */
    protected function assignAdminRole(): void
    {
        $adminRole = $this->getAdminRole();

        if ($adminRole instanceof Model) {
            // Use sync() para evitar duplicatas
            $this->record->roles()->sync([$adminRole->id]);

            // Enviar notificação
            $this->sendRoleAssignmentNotification(
                'Role de Administração atribuída com sucesso!',
                "O usuário {$this->record->name} agora tem permissões de administrador.",
                'heroicon-s-shield-check'
            );
        }
    }

    /**
     * Busca a role de administrador no banco de dados
     */
    protected function getAdminRole(): ?Model
    {
        return Role::where('name', EnumRole::Administracao->value)->first();
    }

    /**
    * Atribui as roles selecionadas ao usuário (exceto admin)
    */
    protected function assignOtherRoles(): void
    {
        if (isset($this->data['Funções']) && ! empty($this->data['Funções'])) {
            // Use sync() para evitar duplicatas
            $this->record->roles()->sync($this->data['Funções']);

            // Buscar os nomes das roles para exibição
            $roleNames = $this->getRoleNames($this->data['Funções']);
            $rolesText = $this->formatRoleNames($roleNames);

            // Enviar notificação
            $this->sendRoleAssignmentNotification(
                'Funções atribuídas com sucesso!',
                "O usuário {$this->record->name} agora tem acesso às funções: {$rolesText}",
                'heroicon-s-user-group'
            );
        }
    }

    /**
     * Busca os nomes das roles pelos IDs
     */
    protected function getRoleNames(array $roleIds): array
    {
        return Role::whereIn('id', $roleIds)->pluck('name')->toArray();
    }

    /**
     * Formata uma lista de nomes de roles para exibição
     */
    protected function formatRoleNames(array $roleNames): string
    {
        if (count($roleNames) <= 1) {
            return $roleNames[0] ?? '';
        }

        return implode(', ', array_slice($roleNames, 0, -1)) . ' e ' . end($roleNames);
    }

    /**
     * Envia uma notificação de atribuição de roles
     */
    protected function sendRoleAssignmentNotification(string $title, string $body, string $icon): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->color('success')
            ->icon($icon)
            ->iconColor('success')
            ->seconds(8)
            ->send();
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
