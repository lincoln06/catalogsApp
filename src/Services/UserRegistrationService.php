<?php

namespace App\Services;

use App\Entity\RegisterRequest;
use Doctrine\ORM\EntityManagerInterface;

class UserRegistrationService
{
    private EntityManagerInterface $entityManager;
    private MailerService $mailerService;
    public function __construct(EntityManagerInterface $entityManager, MailerService $mailerService) {
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
        }
    
    public function allowToRegister(RegisterRequest $registerRequest) : void
    {
        $this->mailerService->sendEmail(
            $registerRequest->getEmail(),
            "Katalogi GABIT - link do rejestracji",
            "Aby dokończyć rejestrację, skopiuj ten link do okna przeglądarki: localhost:8000/register/allowed/{$registerRequest->getHash()}"
        );
    }
    public function deleteRegisterRequest(RegisterRequest $registerRequest) : void
    {
        $this->entityManager->remove($registerRequest);
        $this->entityManager->flush();
    }
}