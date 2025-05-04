<?php

declare(strict_types = 1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Comum a todos ambientes: permissões e o suporte
        $this->seedSupportUserAndPermissions();

        // Apenas em ambientes não-produção: dados de teste
        if (! app()->environment('production')) {
            $this->seedTestData();
        }
    }

    /**
     * Cria apenas o usuário de suporte e permissões básicas
     */
    private function seedSupportUserAndPermissions(): void
    {
        // Sempre cria o tenant suporte
        $this->call([
            SupportTenantSeeder::class,
            SupportUserSeeder::class,
        ]);
    }

    /**
     * Cria dados adicionais de teste
     */
    private function seedTestData(): void
    {
        $this->call([
            TenantSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminPermissionsSeeder::class,
        ]);
    }
}
