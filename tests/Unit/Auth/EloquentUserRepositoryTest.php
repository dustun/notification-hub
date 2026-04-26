<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Auth\Infrastructure\Models\EloquentUser;
use App\Shared\Domain\ValueObjects\Email;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws BindingResolutionException
     */
    public function test_repository_maps_email_verified_at_to_domain_entity(): void
    {
        $user = EloquentUser::query()->create([
            'name' => 'Verified User',
            'email' => 'verified@example.com',
            'password' => Hash::make('StrongPassword123!'),
            'email_verified_at' => now(),
        ]);

        $repository = $this->app->make(UserRepositoryInterface::class);
        $domainUser = $repository->byEmail(new Email($user->email));

        $this->assertNotNull($domainUser);
        $this->assertTrue($domainUser->isEmailVerified());
        $this->assertNotNull($domainUser->emailVerifiedAt);
    }
}
