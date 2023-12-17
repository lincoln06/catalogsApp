<?php

namespace App\Services;

use App\Entity\RegisterRequest;
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
            "Aby dokończyć rejestrację, skopiuj ten link do przeglądarki: ",
            "/register/allowed/",
            $registerRequest->getHash()
        );
    }

    public function deleteRegisterRequest($registerRequest): void
    {
        $this->entityManager->remove($registerRequest);
        $this->entityManager->flush();
    }
//    public function deleteOldRegisterRequests() {
//        $date = new \DateTime('now');
//        $date = $date->modify("-30 day");
//        $logs = $this->entityManager->getRepository(RegisterRequest::class)->findAll();
//        foreach($logs as $log) {
//            if($log->getWhenActionWasDone() < $date) {
//                $this->crudService->deleteEntity($log);
//            }
//        }
//    }
//TODO insert 'date' column in reqister request table
//TODO edit register request creation - it must contain actual date
//TODO implement function that deletes all the requests older than 3 days
}