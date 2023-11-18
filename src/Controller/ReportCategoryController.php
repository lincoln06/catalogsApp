<?php

namespace App\Controller;

use App\Entity\ReportCategory;
use App\Form\ReportCategoryType;
use App\Repository\ReportCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportCategoryController extends MainController
{
    #[Route('/report/category/add', name: 'app_add_report_category')]
    public function addReportCategory(Request $request): Response
    {
        $reportCategory = new ReportCategory();
        $form = $this->createForm(ReportCategoryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reportCategory->setName($form->get('name')->getData());
            $reportCategory->setRole($form->get('role')->getData());
            $this->crudService->persistEntity($reportCategory);
            return $this->redirectToRoute('app_show_report_category');
        }
        return $this->render('report_category/new.html.twig', [
            'caption' => 'Dodawanie kategorii zgłoszeń',
            'form' => $form
        ]);
    }

    #[Route('/report/category/show', name: 'app_show_report_category')]
    public function showReportCategories(ReportCategoryRepository $reportCategoryRepository): Response
    {
        $reportCategories = $reportCategoryRepository->findAll();
        if (!$reportCategories) {
            $message = 'Nic do wyświetlenia';
        } else {
            $message = 'Lista kategorii';
        }
        return $this->render('report_category/index.html.twig', [
            'caption' => $message,
            'categories' => $reportCategories

        ]);
    }

    #[Route('/report/category/delete/{id}', name: 'app_delete_report_category')]
    public function deleteReportCategory(ReportCategoryRepository $reportCategoryRepository, int $id): Response
    {
        $reportCategory = $reportCategoryRepository->find($id);
        if (!$reportCategory) {
            return $this->render('error_page/index.html.twig', [
                'message' => 'Brak danych do usunięcia'
            ]);
        }
        $this->crudService->deleteEntity($reportCategory);
        return $this->redirectToRoute('app_show_report_category');
    }

    #[Route('/report/category/edit/{id}', name: 'app_edit_report_category')]
    public function editReportCategory(Request $request, ReportCategoryRepository $reportCategoryRepository, int $id): Response
    {
        $reportCategory = $reportCategoryRepository->find($id);
        if ($reportCategory) {
            $form = $this->createForm(ReportCategoryType::class, $reportCategory);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $reportCategory->setName($form->get('name')->getData());
                $reportCategory->setRole($form->get('role')->getData());
                $this->crudService->persistEntity($reportCategory);
                return $this->redirectToRoute('app_show_report_category');
            }
            return $this->render('report_category/new.html.twig', [
                'caption' => 'Edycja kategorii zgłoszeń',
                'form' => $form
            ]);
        }
        return $this->render('error_page/index.html.twig', [
            'message' => 'Wystąpił błąd podczas edycji'
        ]);
    }
}

