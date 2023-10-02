<?php

namespace App\Controller;

use App\Form\SystemType;
use App\Repository\SystemRepository;
use App\Services\CatalogHandlingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\System;

class SystemController extends MainController
{

    #[Route('/system/show', name: 'app_show_system')]
    public function show(SystemRepository $systemRepository): Response
    {
        $message = '';
        $systems = $systemRepository->findAll();
        if(!$systems) {
            $message = 'Nic do wyświetlenia';
        }
        return $this->render('add_catalog/list.html.twig', [
            'systems' => $systems,
            'message' => $message
        ]);
    }
    #[Route('/system/add', name: 'app_add_system')]
    public function new(Request $request): Response
    {
        $system = new System();

        $form = $this -> createForm(SystemType::class, $system);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $system->setName($form->get('name')->getData());
            $this->crudService->persistEntity($system);
            return $this -> redirectToRoute('app_show_system');
        }
        return $this->render('add_catalog/index.html.twig', [
            'caption' => 'Dodaj system',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/system/edit/{id}', name: 'app_edit_system')]
    public function edit(Request $request, SystemRepository $systemRepository, int $id): Response
    {
        $system = $systemRepository->find($id);
        $message = '';
        if(!$system) {
            $message = 'Brak danych';
        }
        $form = $this -> createForm(SystemType::class, $system);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $system->setName($data->getName());
            $this->crudService->persistEntity($system);
            return $this -> redirectToRoute('app_show_system');
        }
        return $this->render('add_catalog/index.html.twig', [
            'caption' => 'Edytuj system',
            'form' => $form->createView(),
            'message' => $message
        ]);
    }
    #[Route('/system/delete/{id}', name: 'app_delete_system')]
    public function delete(CatalogHandlingService $catalogHandlingService, SystemRepository $systemRepository, int $id): Response
    {
        $system = $systemRepository->find($id);
        $systemCatalogs = $system->getCatalogs();
        if($systemCatalogs)
        {
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
        return $this->redirectToRoute('app_show_system');
    }
}
