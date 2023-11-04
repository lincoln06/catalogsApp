<?php

namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportSolvedType;
use App\Form\ReportType;
use App\Repository\ReportRepository;
use App\Services\GetReportsListService;
use App\Services\MailerService;
use App\Services\UserPrivilegeValidatingService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ReportController extends MainController
{
    #[Route('/report/show', name: 'app_show_report')]
    public function index(GetReportsListService $getReportsListService): Response
    {
        $reports = $getReportsListService->getReportsList();
        if (!$reports) {
            return $this->render('report/index.html.twig', [
                'caption' => 'Nic do wyświetlenia'
            ]);
        }
        return $this->render('report/index.html.twig', [
            'caption' => 'Lista zgłoszeń',
            'reports' => $reports
        ]);
    }

    #[Route('/report/add', name: 'app_add_report')]
    public function new(Request $request, Security $security): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $report->setReportFrom($security->getUser()->getEmail());
            $report->setCategory($form->get('category')->getData());
            $report->setTopic($form->get('topic')->getData());
            $report->setDescription($form->get('description')->getData());
            $this->crudService->persistEntity($report);
            return $this->render('report/new.html.twig', [
                'caption' => 'Wyślij zgłoszenie',
                'form' => $form,
                'message' => 'Zgłoszenie zostało wysłane'
            ]);
        }
        return $this->render('report/new.html.twig', [
            'caption' => 'Wyślij zgłoszenie',
            'form' => $form,
        ]);
    }

    #[Route('/report/solve/{id}', name: 'app_solve_report')]
    public function solveReport(Request $request, ReportRepository $reportRepository, UserPrivilegeValidatingService $userPrivilegeValidatingService, MailerService $mailerService, int $id): Response
    {
        $report = $reportRepository->find($id);
        if (!$report) {
            return $this->render('error_page/index.html.twig', [
                'message' => 'Brak raportu'
            ]);
        }
        if (!$this->isGranted($report->getCategory()->getRole())) {
            return $this->render('error_page/index.html.twig', [
                'message' => 'Brak uprawnień do obsługi tego zgłoszenia'
            ]);
        }
        $form = $this->createForm(ReportSolvedType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $answerToUser = $form->get('answer')->getData();
            $title = "[Rozwiązanie problemu: {$report->getCategory()->getName()}]";
            $solvedMessage = "Twoje zgłoszenie: \n{$report->getTopic()}\nzostało rozwiązane. \n Odpowiedź do zgłoszenia: \n {$answerToUser}";
            $mailerService->sendEmail($report->getReportFrom(), $title, $solvedMessage);
            $this->crudService->deleteEntity($report);
            return $this->redirectToRoute('app_show_report');
        }
        return $this->render('report/solve.html.twig', [
            'caption' => 'Rozwiązywanie zgłoszenia',
            'form' => $form
        ]);
    }
}
