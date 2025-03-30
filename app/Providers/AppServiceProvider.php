<?php

declare(strict_types = 1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\PulseServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(PulseServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configModels();
        $this->configCommands();
        // $this->configUrls();
        $this->configDate();
    }

    private function configModels(): void
    {
        // Certificar de que todas as propriedades sendo chamadas existam no modelo
        Model::shouldBeStrict();
    }

    // Configura os comandos do banco de dados para proibir a execução de instruções destrutivas
    // quando a aplicação está em execução em um ambiente de produção.

    private function configCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction()
        );
    }

    private function configDate(): void
    {
        Date::use(CarbonImmutable::class);
    }
}
