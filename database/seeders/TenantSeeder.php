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
        // Cria tenant admin para testes
        Tenant::factory()->create([
            'name'   => 'Admin Tenant',
            'domain' => 'admin.localhost.com.br',
            'email'  => 'admin@localhost.com.br',
        ]);
        // Cria 5 tenants de exemplo
        Tenant::factory()
            ->count(20)
            ->create();
    }
}
