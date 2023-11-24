<?php

namespace App\Services;

use App\Entity\Log;
use App\Repository\LogRepository;
use Symfony\Bundle\SecurityBundle\Security;

class LogService
{
    protected Security $security;
    protected CRUDService $crudService;
    protected LogRepository $logRepository;

    public function __construct(Security $security, CRUDService $crudService, LogRepository $logRepository)
    {
        $this->security = $security;
        $this->crudService = $crudService;
        $this->logRepository = $logRepository;
    }

    public function createLog(string $actionType, string $entityType): void
    {
        $userName = $this->security->getUser()->getEmail();
        $actualTime = new \DateTime('now');
        $log = new Log();
        $log->setWhoDidTheAction($userName);
        $log->setActionType($actionType);
        $log->setOnWhatEntity($entityType);
        $log->setWhenActionWasDone($actualTime);
        $this->deleteOldLogs();
        $this->crudService->persistEntity($log);
    }
    private function deleteOldLogs()
    {
        $date = new \DateTime('now');
        $date = $date->modify("-30 day");
        $logs = $this->logRepository->findAll();
        foreach($logs as $log) {
            if($log->getWhenActionWasDone() < $date) {
                $this->crudService->deleteEntity($log);
            }
        }
    }
}