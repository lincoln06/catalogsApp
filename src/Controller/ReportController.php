<?php

namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\ReportRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ReportController extends MainController
{
    #[Route('/report/show', name: 'app_show_report')]
    public function index(ReportRepository $reportRepository): Response
    {
        $reports = $reportRepository->findAll();
        if(!$reports) {
            return $this->render('report/index.html.twig', [
                'title' => 'Nic do wyświetlenia'
                ]);
        }
        return $this->render('report/index.html.twig', [
            'title' => 'Lista zgłoszeń',
            'reports' => $reports
        ]);
    }
    #[Route('/report/add', name: 'app_add_report')]
    public function new(Request $request, Security $security): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $report->setReportFrom($security->getUser()->getEmail());
            $report->setCategory($form->get('category')->getData());
            $report->setTopic($form->get('topic')->getData());
            $report->setDescription($form->get('description')->getData());
            $this->crudService->persistEntity($report);
            return $this->render('report/new.html.twig', [
               'title' => 'Dodawanie zgłoszenia',
               'form' => $form,
               'message' => 'Zgłoszenie zostało wysłane'
            ]);
        }
        return $this->render('report/new.html.twig',[
            'title' => 'Dodawanie zgłoszenia',
            'form' => $form,
        ]);
    }
}
