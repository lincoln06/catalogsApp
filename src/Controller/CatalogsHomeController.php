<?php

namespace App\Controller;

use App\Entity\Catalog;
use App\Form\CatalogType;
use App\Repository\CatalogRepository;
use App\Repository\SystemRepository;
use App\Services\CatalogHandlingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;

class CatalogsHomeController extends MainController
{
    #[Route('/', name: 'app_catalogs_home')]
    public function index(SystemRepository $systemRepository): Response
    {
        $systems=$systemRepository->findAll();
        $catalogs=[];
        foreach($systems as $system) {
            $catalogs[$system->getName()]=$system->getCatalogs();
        }
        return $this->render('catalogs_home/index.html.twig', [
                'systems'=>$systems,
                'catalogs'=>$catalogs
            ]);
    }
    #[Route('/catalog/add', name: 'app_add_catalog')]
    public function new(Request $request, CatalogHandlingService $catalogHandlingService): Response
    {
        $catalog = new Catalog();
        $form = $this -> createForm(CatalogType::class, $catalog);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $catalog = $catalogHandlingService->createOfUpdateCatalog($catalog, $form);
            $this->crudService->persistEntity($catalog);
            return $this -> redirectToRoute('app_catalogs_home');
        }
        return $this->render('catalogs_home/new.html.twig', [
            'caption' => 'Dodaj katalog',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/catalog/edit/{id}', name: 'app_edit_catalog')]
    public function edit(Request $request, CatalogHandlingService $catalogHandlingService, CatalogRepository $catalogRepository, int $id): Response
    {
        $catalog = $catalogRepository->find($id);
        if($catalog) {
            $oldPdfFile = $catalog->getPdfFile();
            $filesystem = new Filesystem();
            $form = $this->createForm(CatalogType::class, $catalog);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $catalog = $catalogHandlingService->createOfUpdateCatalog($catalog, $form);
                $this->crudService->persistEntity($catalog);
                if ($catalog->getPdfFile() !== $oldPdfFile) {

                    $olfPdfFilePath = $this->getParameter('catalogs_directory') . '/' . $oldPdfFile;
                    $filesystem->remove($olfPdfFilePath);
                }
                return $this->redirectToRoute('app_catalogs_home');
            }
            return $this->render('add_catalog/index.html.twig', [
                'caption' => 'Edytuj Katalog',
                'form' => $form->createView()
            ]);
        }
        return $this->render('error_page/index.html.twig', [
           'message' => 'Wystąpił błąd'
        ]);
    }
    #[Route('/catalog/delete/{id}', name: 'app_delete_catalog')]
    public function delete(CatalogRepository $catalogRepository, CatalogHandlingService $catalogHandlingService, int $id): Response
    {
        $catalog = $catalogRepository->find($id);
        if(!$catalog) {
            return $this->render('error_page/index.html.twig', [
               'message' => 'Brak danych'
            ]);
        }
        if(!$catalogHandlingService->deleteCatalogFile($catalog))
        {
            return $this->render('error_page/index.html.twig',[
                'message' => 'Błąd podczas usuwania: Nie znaleziono pliku'
            ]);
        }
        $this->crudService->deleteEntity($catalog);
        return $this->redirectToRoute('app_catalogs_home');
    }
}
