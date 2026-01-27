<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ActiveDamagesWidget;
use App\Filament\Widgets\DocumentExpirationsWidget;
use App\Filament\Widgets\ExpenseByCategoryChart;
use App\Filament\Widgets\ExpenseStatsWidget;
use App\Filament\Widgets\MaintenanceAlertsWidget;
use App\Filament\Widgets\PendingTasksWidget;
use App\Filament\Widgets\UpcomingEventsStatsWidget;
use App\Filament\Widgets\UpcomingMaintenancesWidget;
use App\Filament\Widgets\VehicleStatsWidget;
use App\Http\Middleware\SetLocale;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
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
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()->label(__('admin.navigation.fleet')),
                NavigationGroup::make()->label(__('admin.navigation.operations')),
                NavigationGroup::make()->label(__('admin.navigation.finance')),
                NavigationGroup::make()->label(__('admin.navigation.administration')),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                VehicleStatsWidget::class,
                ExpenseStatsWidget::class,
                ExpenseByCategoryChart::class,
                MaintenanceAlertsWidget::class,
                UpcomingEventsStatsWidget::class,
                PendingTasksWidget::class,
                DocumentExpirationsWidget::class,
                UpcomingMaintenancesWidget::class,
                ActiveDamagesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn () => view('filament.components.locale-switcher')
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
