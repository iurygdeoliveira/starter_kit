<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait TenantScopeTrait
{
    public static function bootTenantScopeTrait(): void
    {
        if (Auth::check()) {
            static::addGlobalScope('tenant_id', fn (Builder $builder) => $builder->where('tenant_id', Auth::user()->tenant_id));
        }
    }
}
