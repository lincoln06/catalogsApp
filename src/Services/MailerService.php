<?php

namespace App\Services;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class MailerService
{
    private string $pageAddress = "https://zajecia.koplin.pl";

    public function sendEmail(string $emailAddress, string $title, string $body, string $path = '', string $hash = ''): void
    {
        $messageBody = $body.' '.$this->pageAddress.$path.$hash;
        $transport = Transport::fromDsn('smtp://gabitcatalogs@gmx.com:Katalogi1!@mail.gmx.com:587');
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from('gabitcatalogs@gmx.com')
            ->to($emailAddress)
            ->subject($title)
            ->text($messageBody);
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }
}