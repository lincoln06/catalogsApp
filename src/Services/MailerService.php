<?php

namespace App\Services;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class MailerService
{
    public function sendEmail(string $emailAddress, string $title, string $body) : void
    {
        $transport = Transport::fromDsn('dsn_for_symfony_mailer');
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from('place_your_email_here')
            ->to($emailAddress)
            ->subject($title)
            ->text($body);
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }
}