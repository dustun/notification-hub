<?php

declare(strict_types=1);

namespace App\Auth\Http\SignUp;

use App\Auth\Application\UseCases\SignUp\SignUpHandler;
use App\Auth\Application\UseCases\SignUp\SignUpInput;
use App\Auth\Infrastructure\Exceptions\NotFoundException;
use OpenApi\Attributes as OA;

class SignUpController
{
    #[OA\Post(
        path: '/api/v1/auth/sign-up',
        operationId: 'authSignUp',
        summary: 'Register a new user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AuthSignUpRequest'),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User successfully registered',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthSignUpResponse'),
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ],
    )]
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
