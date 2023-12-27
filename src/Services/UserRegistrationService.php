<?php

namespace App\Services;

use App\Entity\RegisterRequest;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            "Aby dokończyć rejestrację, skopiuj poniższy link do przeglądarki. Link będzie ważny 2 dni ",
            "/register/allowed/",
            $registerRequest->getHash()
        );
    }

    public function deleteRegisterRequest($registerRequest): void
    {
        $this->entityManager->remove($registerRequest);
        $this->entityManager->flush();
    }
    public function deleteOldRegisterRequests(): void
    {
        $registerRequestRepository = $this->entityManager->getRepository(RegisterRequest::class);
        $date = new DateTime('now');
        $date = $date->modify("-2 day");
        $requests = $registerRequestRepository->findAll();
        foreach($requests as $request) {
            if($request->getDate() < $date && $request->getDate() !== null) {
                $this->entityManager->remove($request);
                $this->entityManager->flush();
            }
        }
    }
}