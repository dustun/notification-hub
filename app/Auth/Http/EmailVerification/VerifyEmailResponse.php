<?php

declare(strict_types=1);

namespace App\Auth\Http\EmailVerification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VerifyEmailResponse extends JsonResource
{
    /**
     * @return array<string, bool|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'verified' => true,
            'message' => 'Email успешно подтвержден',
        ];
    }
}
