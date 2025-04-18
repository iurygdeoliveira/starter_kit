<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;

/**
 * Trait para aplicar em qualquer modelo que precise suportar multi-tenancy.
 * Esta trait combina as funcionalidades de escopo de tenant e associação automática.
 */
trait TenantModelTrait
{
    use TenantScopeTrait;
    use BelongsToTenantTrait;

    /**
     * Define o relacionamento com o tenant ao qual este modelo pertence.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
