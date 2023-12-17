<?php

namespace App\Services;

use App\Entity\RegisterRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

class UserRegistrationService extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private MailerService $mailerService;

    public function __construct(EntityManagerInterface $entityManager, MailerService $mailerService)
    {
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
    }

    public function allowToRegister(RegisterRequest $registerRequest): void
    {
        $this->mailerService->sendEmail(
            $registerRequest->getEmail(),
            "Katalogi GABIT - link do rejestracji",
            "Aby dokończyć rejestrację, skopiuj ten link do przeglądarki: ",
            "/register/allowed/",
            $registerRequest->getHash()
        );
    }

    public function deleteRegisterRequest($registerRequest)
    {
            $this->entityManager->remove($registerRequest);
            $this->entityManager->flush();
    }
}