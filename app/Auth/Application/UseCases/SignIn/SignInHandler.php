<?php

declare(strict_types=1);

namespace App\Auth\Application\UseCases\SignIn;

use App\Auth\Infrastructure\Exceptions\NotFoundException;
use App\Auth\Infrastructure\Repositories\EloquentUserRepository;
use App\Shared\Domain\ValueObjects\Email;
use App\Shared\Infrastructure\Services\HasherService;
use App\Shared\Infrastructure\Services\SanctumTokenCreatorService;
use Exception;

readonly class SignInHandler
{
    public function __construct(
        private EloquentUserRepository     $userRepository,
        private HasherService              $hasherService,
        private SanctumTokenCreatorService $tokenCreator
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
