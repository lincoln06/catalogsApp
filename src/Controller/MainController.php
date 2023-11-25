<?php

namespace App\Controller;

use App\Services\CRUDService;
use App\Services\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
}