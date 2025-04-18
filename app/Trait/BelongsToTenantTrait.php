<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Support\Facades\Auth;

trait BelongsToTenantTrait
{
    public static function bootBelongsToTenantTrait(): void
    {
        static::creating(function ($user): void {
            if (Auth::check()) {
                $user->tenant_id = Auth::user()->tenant_id;
            }
        });

        static::updating(function ($user): void {
            if (Auth::check()) {
                $user->tenant_id = Auth::user()->tenant_id;
            }
        });
    }
}
