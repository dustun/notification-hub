<?php

declare(strict_types=1);

namespace App\Auth\Domain\Entities;

use App\Auth\Domain\ValueObjects\Name;
use App\Shared\Domain\ValueObjects\Email;
use App\Shared\Domain\ValueObjects\Password;
use App\Shared\Domain\ValueObjects\UUID;

readonly class User
{
    public function __construct(
        public UUID $id,
        public Name $name,
        public Email $email,
        public Password $password,
    ) {}
}
