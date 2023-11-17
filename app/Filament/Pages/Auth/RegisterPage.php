<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
//use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Pages\Auth\Register as BaseRegisterPage;
use Rawilk\FilamentPasswordInput\Password;


class Register extends BaseRegisterPage
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Password::make('password')
                    ->label('Password')
                    ->required()
                    ->copyable()
                    ->copyTooltip('Copy password')
                    ->copyIcon('heroicon-o-clipboard')
                    ->regeneratePassword()
                    ->minLength(8)
                    ->regeneratePasswordIconColor('primary'),
                
                Password::make('confirm_password')
                    ->label('Confirm Password')
                    ->required()
                    ->copyable()
                    ->copyTooltip('Copy password')
                    ->copyIcon('heroicon-o-clipboard')



            ]);
    }
}
