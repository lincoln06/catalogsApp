<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportCategoryController extends AbstractController
{
    #[Route('/report/category', name: 'app_report_category')]
    public function index(): Response
    {
        return $this->render('report_category/index.html.twig', [
            'controller_name' => 'ReportCategoryController',
        ]);
    }
}
