<?php

namespace App\Controller;

use App\Entity\System;
use App\Form\SystemType;
use App\Repository\SystemRepository;
use App\Services\CatalogHandlingService;
use App\Services\CheckObjectNameService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends MainController
{

    #[Route('/system/show', name: 'app_show_system')]
    public function showSystems(SystemRepository $systemRepository): Response
    {
        $systems = $systemRepository->findBy(array(), array('name' => 'ASC'));
        return $this->render('system/list.html.twig', [
            'systems' => $systems,
        ]);
    }

    #[Route('/system/add', name: 'app_add_system')]
    public function newSystem(Request $request, CheckObjectNameService $checkObjectNameService): Response
    {
        $system = new System();

        $form = $this->createForm(SystemType::class, $system);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $systemName = $form->get('name')->getData();
            if ($checkObjectNameService->checkIfSystemExists($systemName)) {
                $system->setName($form->get('name')->getData());
                $this->crudService->persistEntity($system);
                $this->logService->createLog(
                    explode('::', $request->attributes->get('_controller'))[1],
                    $system->getName()
                );
                $this->addFlash(
                    'success',
                    'System został dodany'
                );
                return $this->redirectToRoute('app_show_system');
            }
            $error = "System o takiej nazwie już istnieje";
            return $this->render('system/index.html.twig', [
                'caption' => 'Dodawanie systemodawcy',
                'form' => $form->createView(),
                'error' => $error
            ]);
        }
        return $this->render('system/index.html.twig', [
            'caption' => 'Dodawanie systemodawcy',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/system/edit/{id}', name: 'app_edit_system')]
    public function editSystem(Request $request, CheckObjectNameService $checkObjectNameService, SystemRepository $systemRepository, int $id): Response
    {
        $system = $systemRepository->find($id);
        $error = '';
        if (!$system) {
            $error = 'Brak danych';
        }
        $oldName = $system->getName();
        $form = $this->createForm(SystemType::class, $system);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $systemName = $form->get('name')->getData();
            if ($checkObjectNameService->checkIfSystemExists($systemName) || $oldName === $systemName) {
                $system->setName($systemName);
                $this->crudService->persistEntity($system);
                $this->logService->createLog(
                    explode('::', $request->attributes->get('_controller'))[1],
                    $system->getName()
                );
                $this->addFlash(
                    'success',
                    'Zmiany zostały zapisane'
                );
                return $this->redirectToRoute('app_show_system');
            }
            $error = 'System o takiej nazwie już istnieje';
        }
        return $this->render('system/index.html.twig', [
            'caption' => 'Edycja systemodawcy',
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    #[Route('/system/delete/{id}', name: 'app_delete_system')]
    public function deleteSystem(Request $request, CatalogHandlingService $catalogHandlingService, SystemRepository $systemRepository, int $id): Response
    {
        $system = $systemRepository->find($id);
        $systemCatalogs = $system->getCatalogs();
        if (count($systemCatalogs) !== 0) {
            foreach ($systemCatalogs as $systemCatalog) {
                $this->crudService->deleteEntity($systemCatalog);

                if (!$catalogHandlingService->deleteCatalogFile($systemCatalog)) {
                    return $this->render('error_page/index.html.twig', [
                        'message' => 'Błąd podczas usuwania pliku pdf'
                    ]);
                }
            }
        }
        $this->crudService->deleteEntity($system);
        $this->logService->createLog(
            explode('::', $request->attributes->get('_controller'))[1],
            $system->getName()
        );
        $this->addFlash(
            'success',
            'System został usunięty'
        );
        return $this->redirectToRoute('app_show_system');
    }
}
