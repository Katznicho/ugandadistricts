<?php

namespace App\Filament\Pages\Auth;


use Filament\Forms\Form;
//use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Pages\Auth\Login as BaseLoginPage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Rawilk\FilamentPasswordInput\Password;


class Login extends BaseLoginPage
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                Password::make('password')
                    ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
                    ->label('Password')
                    ->required()
                    ->copyable()
                    ->copyTooltip('Copy password')
                    ->copyIcon('heroicon-o-clipboard'),

                $this->getRememberFormComponent(),


            ]);
    }
}
