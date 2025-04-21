<?php

declare(strict_types = 1);

namespace App\Providers\Filament;

use App\Http\Middleware\SetTenantMiddleware;
use App\Models\Tenant;
use Filament\Enums\ThemeMode;
use Filament\Forms\Components\Field;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tables\Columns\Column;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel

            ->bootUsing(function (): void {
                Field::configureUsing(function (Field $field): void {
                    $field->translateLabel();
                });

                Column::configureUsing(function (Column $column): void {
                    $column->translateLabel();
                });
            })
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->colors([
                'primary'   => '#076fd1',
                'secondary' => '#6b7a91',
                'danger'    => '#d5393a',
                'warning'   => '#f76707',
                'success'   => '#2eb347',
                'info'      => '#4398e0',
                'light'     => '#f7f8fc',
            ])
            ->sidebarWidth('15rem')
            // ->tenant(Tenant::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            // ->navigationGroups([
            //     'Administração',
            // ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetTenantMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
