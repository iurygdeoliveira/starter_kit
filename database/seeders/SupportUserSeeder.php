<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupportUserSeeder extends Seeder
{
    public function run(): void
    {
        $suporteTenant = Tenant::where('name', 'Elshamah Tecnologia LTDA')->first();

        User::factory()->create([
            'uuid'      => fake('pt_BR')->uuid(),
            'name'      => 'suporte',
            'email'     => 'suporte@elshamahtec.com.br',
            'cpf'       => fake('pt_BR')->unique()->cpf(),
            'phone'     => fake('pt_BR')->unique()->phoneNumber(),
            'password'  => bcrypt('password'),
            'tenant_id' => $suporteTenant->id,
        ]);
    }
}
