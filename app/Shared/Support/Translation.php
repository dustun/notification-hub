<?php

declare(strict_types=1);

namespace App\Shared\Support;

class Translation
{
    public static function get(string $key): string
    {
        $translated = __($key);

        return is_string($translated)
            ? $translated
            : $key;
    }
}
