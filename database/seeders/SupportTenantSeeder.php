<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class SupportTenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::factory()->create([
            'name'  => 'Elshamah Tecnologia LTDA',
            'email' => 'administração@elshamahtec.com.br',
            'phone' => fake('pt_BR')->unique()->phoneNumber(),
            'cnpj'  => fake('pt_BR')->unique()->cnpj(),
            'uuid'  => fake('pt_BR')->unique()->uuid(),
        ]);
    }
}
