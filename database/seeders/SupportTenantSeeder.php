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
            'crc'   => $this->crcFaker(),
            'uuid'  => fake('pt_BR')->unique()->uuid(),
        ]);
    }

    public function crcFaker(): string
    {
        $faker = \Faker\Factory::create('pt_BR');

        // Gerar CRC no formato UF-NNNNNN/O-D (ex: SP-123456/O-7)
        $crc = function () use ($faker) {
            $estados = ['AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
                'MG', 'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN',
                'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO'];

            $uf        = $faker->randomElement($estados);
            $numero    = str_pad((string)$faker->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT);
            $categoria = $faker->randomElement(['O', 'T']); // O=Contador, T=Técnico
            $digito    = $faker->numberBetween(1, 9);

            return "{$uf}-{$numero}/{$categoria}-{$digito}";
        };

        return $crc();
    }
}
