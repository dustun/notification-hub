<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Auth\Infrastructure\Models\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout_and_revoke_current_token(): void
    {
        $user = EloquentUser::query()->create([
            'name' => 'Imran',
            'email' => 'imran@example.com',
            'password' => Hash::make('StrongPassword123!'),
        ]);

        $plainTextToken = $user->createToken('auth_token')->plainTextToken;

        $response = $this
            ->withToken($plainTextToken)
            ->postJson('/api/v1/auth/logout');

        $response
            ->assertOk()
            ->assertJsonPath('data.loggedOut', true)
            ->assertJsonPath('data.message', 'Вы успешно вышли из аккаунта.');

        $freshUser = $user->fresh();

        $this->assertInstanceOf(EloquentUser::class, $freshUser);
        $this->assertSame(0, $freshUser->tokens()->count());
    }
}
