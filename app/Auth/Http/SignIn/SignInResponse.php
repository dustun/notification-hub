<?php

declare(strict_types=1);

namespace App\Auth\Http\SignIn;

use App\Auth\Application\UseCases\SignIn\SignInOutput;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin SignInOutput */
class SignInResponse extends JsonResource
{
    /**
     * @return array{token: string, userId: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'userId' => $this->userId,
        ];
    }
}
