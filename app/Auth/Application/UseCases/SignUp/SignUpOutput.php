<?php

declare(strict_types=1);

namespace App\Auth\Application\UseCases\SignUp;

readonly class SignUpOutput
{
    public function __construct(
        public string $token
    ) {}
}
