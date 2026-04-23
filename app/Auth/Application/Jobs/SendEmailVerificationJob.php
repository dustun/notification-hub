<?php

declare(strict_types=1);

namespace App\Auth\Application\Jobs;

use App\Mail\Infrastructure\Services\LaravelMailSender;
use App\Shared\Domain\ValueObjects\Email;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailVerificationJob implements ShouldQueue
{
    public function __construct(
        public string $userId,
        public string $email,
        public string $name,
        public string $verificationUrl,
    ) {}

    public function handle(
        LaravelMailSender $mailSender
    ): void {
        $mailSender->send(
            new Email($this->email),
            'Верификация почты',
            'emails.email-verification',
            [
                'username' => $this->name,
                'verificationUrl' => $this->verificationUrl,
            ]
        );
    }
}
