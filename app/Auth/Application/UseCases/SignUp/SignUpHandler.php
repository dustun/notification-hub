<?php

declare(strict_types=1);

namespace App\Auth\Application\UseCases\SignUp;

use App\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Auth\Domain\Entities\User;
use App\Auth\Domain\Events\UserRegistered;
use App\Auth\Domain\ValueObjects\Name;
use App\Auth\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Domain\ValueObjects\Email;
use App\Shared\Domain\ValueObjects\Password;
use App\Shared\Domain\ValueObjects\UUID;
use App\Shared\Infrastructure\Services\HasherService;
use App\Shared\Infrastructure\Services\SanctumTokenCreatorService;

readonly class SignUpHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private HasherService           $hasherService,
        private SanctumTokenCreatorService $sanctumTokenCreatorService
    ) {}

    /**
     * @throws NotFoundException
     */
    public function execute(
        SignUpInput $data
    ): SignUpOutput {
        $email = new Email($data->email);

        if ($this->userRepository->existByEmail($email)) {
            throw new NotFoundException('Пользователь с таким email уже существует!');
        }

        $domainUser = new User(
            id: UUID::generate(),
            name: new Name($data->name),
            email: new Email($data->email),
            password: new Password(
                $this->hasherService->hash(
                    $data->password
                )
            )
        );

        $this->userRepository->save($domainUser);

        event(new UserRegistered($domainUser));

        $token = $this->sanctumTokenCreatorService->create(
            userId: $domainUser->id->value(),
            tokenName: 'web'
        );

        return SignUpOutput::from([
            $token,
        ]);
    }
}
