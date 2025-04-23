<?php

namespace App\Trait;

use Illuminate\Support\Facades\Auth;

trait LoggedUserTrait
{
    /**
     * Verifica se o usuário está autenticado, tem um tenant_id e não há tenant na sessão
     *
     * @return bool
     */
    public function LoggedUser(): bool
    {
        return (Auth::check() && Auth::user()->tenant_id && !session('tenant'));
    }
}
