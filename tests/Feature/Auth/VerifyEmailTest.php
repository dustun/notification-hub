<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Auth\Infrastructure\Models\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_verification_link_marks_email_as_verified(): void
    {
        $user = new EloquentUser();
        $user->fill([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => Hash::make('StrongPassword123!'),
            'email_verified_at' => null,
        ]);
        $user->save();

        $url = URL::temporarySignedRoute(
            'verify-email',
            now()->addMinutes(60),
            ['user' => $user->id]
        );

        $response = $this->getJson($url);

        $response
            ->assertOk()
            ->assertJsonPath('data.verified', true);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $freshUser = $user->fresh();

        $this->assertInstanceOf(EloquentUser::class, $freshUser);
        $this->assertTrue($freshUser->email_verified_at !== null);
    }
}
