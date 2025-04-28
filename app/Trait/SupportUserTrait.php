<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Support\Facades\Auth;

trait SupportUserTrait
{
    use UserLoogedTrait;

    /**
     * Verifica se o usuário atual é um usuário de suporte.
     */
    public static function isSupportUser(): bool
    {
        return Auth::check() && (Auth::user()->email === 'suporte@elshamahtec.com.br');
    }
}
