<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('personal_id')
                            ->label('個人番号'),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'personal_id' => $data['personal_id'],
            'password' => 'password',
        ];
    }
}
