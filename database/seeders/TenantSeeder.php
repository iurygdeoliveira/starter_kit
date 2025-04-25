<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Executa o seeder para criar tenants de exemplo.
     */
    public function run(): void
    {
        Tenant::factory()
            ->count(20)
            ->create();
    }
}
