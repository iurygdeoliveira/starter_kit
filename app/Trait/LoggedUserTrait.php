<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Support\Facades\Auth;

trait LoggedUserTrait
{
    /**
     * Verifica se exite usario autenticado e não há tenant na sessão
     *
     * @return bool
     */
    public function LoggedUser(): bool
    {
      
        return (Auth::check() && session('tenant'));
    }
}
