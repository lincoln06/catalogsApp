<?php

namespace App\Controller;

use App\Services\CRUDService;
use App\Services\LogService;
use App\Services\NotificationsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected CRUDService $crudService;
    protected LogService $logService;

    public function __construct(EntityManagerInterface $entityManager, CRUDService $crudService, LogService $logService)
    {
        $this->entityManager = $entityManager;
        $this->crudService = $crudService;
        $this->logService = $logService;
    }

    #[Route('/get-notifications', name: 'app_get_notifications')]
    public function getNotifications(NotificationsService $notificationsService): JsonResponse
    {
        $notifications = $notificationsService->getNotifications();
        return new JsonResponse($notifications);
    }
}