<?php

declare(strict_types = 1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\CND;
use App\Filament\Pages\Contabil;
use App\Filament\Pages\Financeiro;
use App\Filament\Pages\Fiscal;
use App\Filament\Pages\Indicadores;
use App\Filament\Pages\Pessoal;
use App\Filament\Pages\Portal;
use App\Filament\Pages\Processos;
use App\Filament\Pages\Suporte;
use App\Filament\Resources\ClientResource;
use App\Filament\Resources\TaskResource;
use App\Filament\Resources\TenantResource;
use App\Filament\Resources\UserResource;
use Filament\Enums\ThemeMode;
use Filament\Forms\Components\Field;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tables\Columns\Column;
use Filament\Widgets\FilamentInfoWidget;
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
            ->spa()
            ->databaseTransactions()
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration(Register::class)
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
            ->theme(asset('css/filament/admin/theme.css'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make('Dashboard')
                        ->icon('icon-dashboard')
                        ->items([
                            ...Indicadores\Indicadores::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Administração')
                        ->icon('icon-administracao')
                        ->items([
                            ...TenantResource::getNavigationItems(),
                            ...UserResource::getNavigationItems(),
                            ...ClientResource::getNavigationItems(),
                            ...TaskResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Consultas')
                        ->icon('icon-consultas')
                        ->items([
                            ...CND\cnd::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Contábil')
                        ->icon('icon-contabil')
                        ->items([
                            ...Contabil\LucroArbitrado::getNavigationItems(),
                            ...Contabil\LucroPresumido::getNavigationItems(),
                            ...Contabil\LucroReal::getNavigationItems(),
                            ...Contabil\Mei::getNavigationItems(),
                            ...Contabil\SimplesNacional::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Financeiro')
                        ->icon('icon-financeiro')
                        ->items([
                            ...Financeiro\Financeiro::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Fiscal')
                        ->icon('icon-fiscal')
                        ->items([
                            ...Fiscal\LucroArbitrado::getNavigationItems(),
                            ...Fiscal\LucroPresumido::getNavigationItems(),
                            ...Fiscal\LucroReal::getNavigationItems(),
                            ...Fiscal\Mei::getNavigationItems(),
                            ...Fiscal\SimplesNacional::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Pessoal')
                        ->icon('heroicon-s-user-group')
                        ->items([
                            ...Pessoal\Ferias::getNavigationItems(),
                            ...Pessoal\Folha::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Portal do Cliente')
                        ->icon('icon-portal')
                        ->items([
                            ...Portal\Consulta::getNavigationItems(),
                            ...Portal\Contabil::getNavigationItems(),
                            ...Portal\Fiscal::getNavigationItems(),
                            ...Portal\Pessoal::getNavigationItems(),
                            ...Portal\Processos::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Processos')
                        ->icon('icon-processo')
                        ->items([
                            ...Processos\Abertura::getNavigationItems(),
                            ...Processos\Alteracao::getNavigationItems(),
                            ...Processos\Outros::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Suporte')
                        ->icon('icon-suporte')
                        ->items([
                            ...Suporte\Chamados::getNavigationItems(),
                            ...Suporte\Documentacao::getNavigationItems(),
                            ...Suporte\Melhoria::getNavigationItems(),
                        ]),
                ]);
            });
    }
}
