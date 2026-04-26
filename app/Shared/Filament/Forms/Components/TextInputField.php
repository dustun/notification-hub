<?php

declare(strict_types=1);

namespace App\Shared\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class TextInputField
{
    public static function make(string $name): TextInput
    {
        return TextInput::make($name)
            ->translateLabel()
            ->string()
            ->required()
            ->maxLength(255);
    }
}
