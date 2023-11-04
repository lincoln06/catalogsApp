<?php

namespace App\Controller;

use App\Services\CRUDService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected CRUDService $crudService;

    public function __construct(EntityManagerInterface $entityManager, CRUDService $crudService)
    {
        $this->entityManager = $entityManager;
        $this->crudService = $crudService;
    }
}