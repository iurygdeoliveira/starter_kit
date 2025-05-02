<?php

declare(strict_types = 1);

namespace App\Models;

use App\Trait\BelongsToTenantTrait;
use App\Trait\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Client extends Model implements Auditable
{
    //
    use BelongsToTenantTrait;
    use UuidTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tenant_id',
        'uuid',

        'name',
        'cnpj',
        'activity',
        'city',
        'state',
        'regime',
    ];

    protected $hidden = [
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d/m/Y H:i',
            'updated_at' => 'datetime:d/m/Y H:i',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
