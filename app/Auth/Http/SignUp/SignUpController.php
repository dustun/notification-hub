<?php

declare(strict_types=1);

namespace App\Auth\Http\SignUp;

use App\Auth\Application\UseCases\SignUp\SignUpHandler;
use App\Auth\Application\UseCases\SignUp\SignUpInput;
use App\Auth\Infrastructure\Exceptions\NotFoundException;

class SignUpController
{
    /**
     * @throws NotFoundException
     */
    public function __invoke(
        SignUpRequest $request,
        SignUpHandler $handler
    ): SignUpResponse {
        $data = SignUpInput::from(
            $request->all()
        );

        $response = $handler->execute($data);

        return SignUpResponse::make($response);
    }
}
