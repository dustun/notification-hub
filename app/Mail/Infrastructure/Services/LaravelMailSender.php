<?php

declare(strict_types=1);

namespace App\Mail\Infrastructure\Services;

use App\Mail\Application\Contracts\MailSender;
use App\Mail\Infrastructure\Mails\GenericMail;
use App\Shared\Domain\ValueObjects\Email;
use Illuminate\Support\Facades\Mail;

class LaravelMailSender implements MailSender
{
    /**
     * @param Email $to
     * @param string $subject
     * @param string $view
     * @param array<string, mixed> $data
     * @return void
     */
    public function send(
        Email  $to,
        string $subject,
        string $view,
        array  $data = []
    ): void {
        Mail::to($to->value())
            ->send(
                new GenericMail($subject, $view, $data)
            );
    }
}
