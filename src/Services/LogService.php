<?php

namespace App\Services;

use App\Entity\Log;
use Symfony\Bundle\SecurityBundle\Security;

class LogService
{
    protected Security $security;
    protected CRUDService $crudService;

    public function __construct(Security $security, CRUDService $crudService)
    {
        $this->security = $security;
        $this->crudService = $crudService;
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
//        var_dump($userName);
//        echo('<br>');
//        var_dump($actionType);
//        echo('<br>');
//        var_dump($entityType);
//        echo('<br>');
//        var_dump($actualTime);
//        echo('<br>');
//        var_dump($log);
//        die();
        $this->crudService->persistEntity($log);
    }
}