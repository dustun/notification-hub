<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Auth\Infrastructure\Models\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SignInTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_sign_in_with_valid_credentials(): void
    {
        $user = EloquentUser::query()->create([
            'name' => 'Imran',
            'email' => 'imran@example.com',
            'password' => Hash::make('StrongPassword123!'),
        ]);

        $response = $this->postJson('/api/v1/auth/sign-in', [
            'email' => 'imran@example.com',
            'password' => 'StrongPassword123!',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.userId', $user->id);

        $token = $response->json('data.token');

        $this->assertIsString($token);
        $this->assertNotSame('', $token);
    }
}
