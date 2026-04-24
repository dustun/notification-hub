<?php

declare(strict_types=1);

namespace App\Auth\Application\UseCases\SignIn;

use App\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Auth\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Domain\Contracts\HasherInterface;
use App\Shared\Domain\Contracts\TokenCreatorInterface;
use App\Shared\Domain\ValueObjects\Email;
use Exception;

readonly class SignInHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private HasherInterface $hasherService,
        private TokenCreatorInterface $tokenCreator
    ) {}

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function execute(
        SignInInput $data
    ): SignInOutput {
        $user = $this->userRepository->byEmail(
            new Email($data->email)
        );

        if (!$user) {
            throw new NotFoundException('Пользователь не найден!');
        }

        $isValidPassword = $this->hasherService->verify(
            plain: $data->password,
            hashed: $user->password->value()
        );

        if (!$isValidPassword) {
            throw new Exception('Неверный логин или пароль!');
        }

        $token = $this->tokenCreator->create(
            userId: $user->id->value(),
            tokenName: 'auth_token',
        );

        return SignInOutput::from([
            'token' => $token,
            'userId' => $user->id->value(),
        ]);
    }
}
