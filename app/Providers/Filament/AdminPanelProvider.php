<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Filament\Forms\Components\FileUpload;
use Illuminate\Validation\Rules\Password;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Reset;
use Filament\Notifications\Auth\ResetPassword;
use Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin;




class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->passwordReset(Reset::class)
            ->colors([
                'primary' => Color::Amber,
                'success' => Color::Green,
                'danger' => Color::Red,
                'warning' => Color::Yellow,
                'info' => Color::Blue,
                'secondary' => Color::Gray,

            ])
            ->navigationGroups([
                'Uganda Data',
                'Roles and Permissions',

            ])
            ->plugins([
                FilamentAuthenticationLogPlugin::make(),
                new \RickDBCN\FilamentEmail\FilamentEmail(),
                \FilipFonal\FilamentLogManager\FilamentLogManager::make(),
                FilamentSpatieRolesPermissionsPlugin::make(),
                \Hasnayeen\Themes\ThemesPlugin::make(),
                // PasswordButtonAction::make('secure_action')->action('doSecureAction'),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: true,
                        hasAvatars: true,
                        slug: 'my-profile',
                    )

                    ->passwordUpdateRules(
                        rules: [Password::default()->mixedCase()->uncompromised(3)],
                        requiresCurrentPassword: true,
                    )
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->disableLabel())
                    // ->avatarUploadComponent(fn () => FileUpload::make('avatar_url')->disk('profile-photos'))
                    ->enableTwoFactorAuthentication()
                    ->enableSanctumTokens(
                        permissions: ['*', 'create', 'read', 'update', 'delete', 'list', 'view'],
                    ),

            ])
            ->brandName('Uganda Data')
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                // Widgets\TotalDistrictsOverView::class,
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
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
