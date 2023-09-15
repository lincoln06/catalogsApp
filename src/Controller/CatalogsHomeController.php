<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogsHomeController extends AbstractController
{
    #[Route('/catalogs/home', name: 'app_catalogs_home')]
    public function index(): Response
    {
        return $this->render('catalogs_home/index.html.twig', [
            'controller_name' => 'CatalogsHomeController',
        ]);
    }
}
