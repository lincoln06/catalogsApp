<?php

namespace App\Services;

use App\Entity\RegisterRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class UserRegistrationService
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        }
    
    public function allowToRegister(RegisterRequest $registerRequest) : void
    {
        $transport = Transport::fromDsn('smtp://gabitcatalogs@gmx.com:Katalogi1!@mail.gmx.com:587');
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from('gabitcatalogs@gmx.com')
            ->to("{$registerRequest->getEmail()}")
            ->subject('Katalogi GABIT - link do rejestracji')
            ->text("Link: localhost:8000/register/allowed/{$registerRequest->getHash()}");
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            var_dump($email);
            die();
        }
    }
    public function deleteRegisterRequest(RegisterRequest $registerRequest) : void
    {
        $this->entityManager->remove($registerRequest);
        $this->entityManager->flush();
    }
}