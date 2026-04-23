<?php

declare(strict_types=1);

namespace App\Auth\Http\EmailVerification;

use App\Auth\Application\UseCases\EmailVerification\VerifyEmailHandler;
use App\Auth\Application\UseCases\EmailVerification\VerifyEmailInput;
use App\Auth\Infrastructure\Exceptions\NotFoundException;

class VerifyEmailController
{
    /**
     * @throws NotFoundException
     */
    public function __invoke(
        VerifyEmailRequest $request,
        VerifyEmailHandler $handler,
    ): VerifyEmailResponse {
        $result = $handler->execute(
            VerifyEmailInput::from([
                'userId' => $request->query('user'),
            ])
        );

        return VerifyEmailResponse::make($result);
    }
}
