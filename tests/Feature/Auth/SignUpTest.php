<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Auth\Application\Jobs\SendEmailVerificationJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SignUpTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_sign_up_and_queue_email_verification_job(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/v1/auth/sign-up', [
            'name' => 'Imran',
            'email' => 'imran@example.com',
            'password' => 'StrongPassword123!',
            'passwordConfirmation' => 'StrongPassword123!',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['token'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'imran@example.com',
        ]);

        Queue::assertPushed(
            SendEmailVerificationJob::class,
            fn(SendEmailVerificationJob $job): bool => $job->email === 'imran@example.com'
                && $job->queue === 'notifications'
        );
    }
}
