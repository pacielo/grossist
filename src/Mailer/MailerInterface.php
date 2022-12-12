<?php

namespace App\Mailer;

interface MailerInterface
{
    public function sendMail(array $from, array $to, string $purpose, string $body,array $Cc = null): ?bool;
}
