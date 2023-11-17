<?php

namespace App\Filament\Pages\Auth;


use Filament\Forms\Form;

use Filament\Pages\Auth\PasswordReset\ResetPassword as BaseResetPassword;
use Rawilk\FilamentPasswordInput\Password;


class Reset extends BaseResetPassword
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                Password::make('password')
                    ->label('Password')
                    ->same('passwordConfirmation')
                    ->required()
                    ->copyable()
                    ->copyTooltip('Copy password')
                    ->copyIcon('heroicon-o-clipboard'),

                Password::make('passwordConfirmation')
                    ->label('Password')
                    ->required()
                    ->copyable()
                    ->copyTooltip('Copy password')
                    ->copyIcon('heroicon-o-clipboard'),



            ]);
    }
}

// protected function getPasswordConfirmationFormComponent(): Component
//     {
//         return TextInput::make('passwordConfirmation')
//             ->label(__('filament-panels::pages/auth/password-reset/reset-password.form.password_confirmation.label'))
//             ->password()
//             ->required()
//             ->dehydrated(false);
//     }
