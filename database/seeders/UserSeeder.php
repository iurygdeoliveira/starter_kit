<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Executa o seeder para criar usuários de exemplo.
     * Cria usuários associados aos tenants existentes.
     */
    public function run(): void
    {
        // Obtém todos os tenants ou cria um se não existir nenhum
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $tenants = Tenant::factory()->count(20)->create();
        }

        $adminTenant = Tenant::find(1);
        User::factory()->create([
            'name'      => 'Admin User',
            'email'     => 'admin@localhost.com.br',
            'cpf'       => fake('pt_BR')->unique()->cpf(),
            'password'  => bcrypt('password'),
            'verified'  => true,
            'tenant_id' => $adminTenant->id,
        ]);

        // Cria 2 usuários para cada tenant
        $tenants->each(function ($tenant): void {
            User::factory()
                ->count(2)
                ->forTenant($tenant)
                ->create();
        });
    }
}
