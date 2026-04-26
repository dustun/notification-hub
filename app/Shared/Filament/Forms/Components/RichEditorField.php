<?php

declare(strict_types=1);

namespace App\Shared\Filament\Forms\Components;

use Filament\Forms\Components\RichEditor;

class RichEditorField
{
    public static function make(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->translateLabel()
            ->nullable()
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike'],
                ['h2', 'h3', 'bulletList', 'orderedList'],
                ['blockquote', 'codeBlock', 'link'],
                ['undo', 'redo'],
            ]);
    }
}
