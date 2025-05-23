<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Role as EnumRole;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public $isAdminEnabled = false;

    #[On('admin-toggle-changed')]
    public function handleAdminToggleChanged($state): void
    {
        // Atualiza a variável quando o toggle muda
        $this->isAdminEnabled = $state;

        // Força uma atualização da UI
        $this->dispatch('refresh');
    }

    // Sobrescreva o método getRelationManagers para controlar quais são exibidos
    #[\Override]
    public function getRelationManagers(): array
    {
        // Verifica se o usuário já tem a role de Administração
        if ($this->record && $this->record->hasRole(EnumRole::Administracao->value)) {
            return []; // Não mostra nenhum relation manager
        }

        // Se o toggle is_admin estiver ativado, não mostrar o RolesRelationManager
        if ($this->isAdminEnabled) {
            return [];
        }

        // Caso contrário, mostrar o RolesRelationManager normalmente
        return [
            RolesRelationManager::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voltar')
                ->label('Voltar')
                ->color('secondary')
                ->icon('heroicon-s-arrow-long-left')
                ->url(UserResource::getUrl('index')),
            DeleteAction::make()
                ->label('Excluir Funcionário')
                ->hidden(fn (): bool => Auth::id() === $this->record->id),
            $this->getCancelFormAction()
                ->color('danger')
                ->outlined(),
        ];
    }

    // Redireciona para a página de listagem (index) após editar o usuário
    #[\Override]
    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }

    #[\Override]
    protected function getSavedNotification(): ?Notification
    {
        // Retorne sua notificação personalizada
        return Notification::make()
            ->title('Usuário atualizado com sucesso!')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->iconColor('success')
            ->seconds(8)
            ->success();
    }

    #[\Override]
    protected function getFormActions(): array
    {
        // parent::getFormActions();

        return [
            // Action::make('voltar')
            //     ->label('Voltar')
            //     ->color('secondary')
            //     ->icon('heroicon-s-arrow-long-left')
            //     ->url(UserResource::getUrl('index')),
            // $this->getSaveFormAction(),
            // $this->getCancelFormAction()
            //     ->color('danger')
            //     ->outlined(),
        ];
    }
}
