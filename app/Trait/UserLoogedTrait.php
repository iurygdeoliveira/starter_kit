<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Support\Facades\Auth;

trait UserLoogedTrait
{
    public static function isUserLoggedIn(): bool
    {
        return Auth::check() && (Auth::user() !== null);
    }
}
