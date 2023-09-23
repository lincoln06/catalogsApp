<?php

namespace App\Services;

use App\Entity\RegisterRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class UserRegistrationService
{
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager) {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        }
    
    public function allowToRegister(RegisterRequest $registerRequest) {
        $email = (new Email())
            ->from('gabitcatalogs@gmx.com')
            ->to("{$registerRequest->getEmail()}")
            ->subject('Katalogi GABIT - lonk do rejestracji')
            ->html("<p>Aby się zarejestrować, kliknij w link: <a href='localhost:8000/register/allowed/{$registerRequest->getHash()}'></a></p>");
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            var_dump($email);
            die();
        }
        $this->deleteRegisterRequest($registerRequest);
    }
    public function deleteRegisterRequest(RegisterRequest $registerRequest) {
        $this->entityManager->remove($registerRequest);
        $this->entityManager->flush();
    }
}