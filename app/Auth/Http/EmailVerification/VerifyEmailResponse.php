<?php

declare(strict_types=1);

namespace App\Auth\Http\EmailVerification;

use App\Auth\Application\UseCases\EmailVerification\VerifyEmailOutput;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin VerifyEmailOutput */
class VerifyEmailResponse extends JsonResource
{
    /**
     * @return array{verified: bool, message: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'verified' => (bool) data_get($this->resource, 'verified'),
            'message' => 'Email успешно подтвержден',
        ];
    }
}
