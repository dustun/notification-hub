<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Repositories;

use App\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Auth\Domain\Entities\User as DomainUser;
use App\Auth\Domain\ValueObjects\Name;
use App\Auth\Infrastructure\Models\EloquentUser;
use App\Shared\Domain\ValueObjects\Email;
use App\Shared\Domain\ValueObjects\Password;
use App\Shared\Domain\ValueObjects\UUID;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function byId(UUID $id): ?DomainUser
    {
        $model = EloquentUser::query()
            ->where('id', $id->value())
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function byEmail(Email $email): ?DomainUser
    {
        $model = EloquentUser::query()
            ->where('email', $email->value())
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function existByEmail(Email $email): bool
    {
        return EloquentUser::query()
            ->where('email', $email->value())
            ->exists();
    }

    public function save(DomainUser $model): void
    {
        EloquentUser::query()->updateOrCreate(
            ['id' => $model->id->value()],
            [
                'name' => $model->name->value(),
                'email' => $model->email->value(),
                'password' => $model->password->value(),
                'email_verified_at' => $model->emailVerifiedAt,
            ]
        );
    }

    private function toDomain(EloquentUser $model): DomainUser
    {
        return new DomainUser(
            id: new UUID($model->id),
            name: new Name($model->name),
            email: new Email($model->email),
            password: new Password($model->password),
            emailVerifiedAt: $model->email_verified_at?->toDateTimeImmutable(),
        );
    }
}
