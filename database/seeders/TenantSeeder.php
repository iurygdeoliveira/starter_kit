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
            'name'  => 'Admin Tenant',
            'email' => 'admin@localhost.com.br',
            'cnpj'  => fake('pt_BR')->unique()->cnpj(),
            'code'  => fake('pt_BR')->unique()->uuid(),
        ]);

        Tenant::factory()
            ->count(20)
            ->create();
    }
}
