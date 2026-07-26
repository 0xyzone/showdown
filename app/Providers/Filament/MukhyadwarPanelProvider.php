<?php

namespace App\Providers\Filament;

use App\Filament\Mukhyadwar\Widgets\OngoingTournamentWidget;
use App\Filament\Mukhyadwar\Widgets\ParticipantStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;

class MukhyadwarPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('mukhyadwar')
            ->path('mukhyadwar')
            ->viteTheme('resources/css/filament/mukhyadwar/theme.css')
            ->authGuard('participant')
            ->login()
            ->registration()
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->userMenuItems([
                'edit-profile' => MenuItem::make()
                    ->label('Edit Profile')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-adjustments-vertical'),
            ])
            ->discoverResources(in: app_path('Filament/Mukhyadwar/Resources'), for: 'App\\Filament\\Mukhyadwar\\Resources')
            ->discoverPages(in: app_path('Filament/Mukhyadwar/Pages'), for: 'App\\Filament\\Mukhyadwar\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Mukhyadwar/Widgets'), for: 'App\\Filament\\Mukhyadwar\\Widgets')
            ->widgets([
                OngoingTournamentWidget::class,
                ParticipantStatsOverview::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->slug('edit-my-profile')
                    ->setTitle('Edit My Profile')
                    ->setNavigationLabel('Edit My Profile')
                    ->setNavigationGroup('Profile')
                    ->setIcon('heroicon-o-user')
                    ->shouldRegisterNavigation(true)
                    ->shouldShowEmailForm()
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowSanctumTokens()
                    ->shouldShowBrowserSessionsForm()
                    ->shouldShowAvatarForm(),
            ]);
    }
}
