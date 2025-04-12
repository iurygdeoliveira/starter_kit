<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name      = fake('pt_BR')->name();
        $nameParts = explode(' ', $name);

        // Pega segundo e terceiro nomes se existirem
        $emailName = '';

        if (isset($nameParts[1])) {
            $emailName .= Str::lower($nameParts[1]);
        }

        if (isset($nameParts[2])) {
            $emailName .= '.' . Str::lower($nameParts[2]);
        }

        // Se não tiver partes suficientes, usa um fallback
        if (empty($emailName)) {
            $emailName = Str::lower($nameParts[0]);
        }

        return [
            'name'      => $name,
            'email'     => $emailName . '@' . fake('pt_BR')->domainName(),
            'password'  => static::$password ??= Hash::make('password'),
            'verified'  => false,
            'tenant_id' => Tenant::factory(),
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
