<?php

namespace App\Controller;

use App\Entity\Catalog;
use App\Form\CatalogType;
use App\Repository\CatalogRepository;
use App\Services\CatalogHandlingService;
use App\Services\CheckObjectNameService;
use App\Services\GetAllCatalogsService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogsHomeController extends MainController
{
    #[Route('/', name: 'app_catalogs_home')]
    public function index(GetAllCatalogsService $getAllCatalogsService): Response
    {
        return $this->render('catalogs_home/index.html.twig', [
            'systems' => $getAllCatalogsService->getSystemsWithCatalogs(),
            'catalogs' => $getAllCatalogsService->getAllCatalogs()
        ]);
    }

    #[Route('/catalog/add', name: 'app_add_catalog')]
    public function newCatalog(Request $request, CatalogHandlingService $catalogHandlingService, CheckObjectNameService $checkObjectNameService): Response
    {
        $catalog = new Catalog();
        $form = $this->createForm(CatalogType::class, $catalog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $catalog = $catalogHandlingService->createOfUpdateCatalog($catalog, $form);
            if ($catalog !== null) {
                if($catalog->getDateAdded() <= new \DateTime()) {

                    if($checkObjectNameService->checkIfCatalogExists($catalog->getName())) {
                        $this->logService->createLog(
                            explode('::', $request->attributes->get('_controller'))[1],
                            $catalog->getSystem()->getName() . ': ' . $catalog->getName()
                        );
                        $this->crudService->persistEntity($catalog);
                        $this->addFlash(
                            'success',
                            'Katalog został dodany'
                        );

                        return $this->redirectToRoute('app_catalogs_home');
                    } else {
                        return $this->render('catalogs_home/new.html.twig', [
                            'caption' => 'Dodawanie katalogu',
                            'error' => 'Katalog o takiej nazwie już istnieje',
                            'form' => $form->createView(),
                        ]);
                    }
                } else {
                    return $this->render('catalogs_home/new.html.twig', [
                        'caption' => 'Dodawanie katalogu',
                        'error' => 'Wprowadzono nieprawidłową datę',
                        'form' => $form->createView(),
                    ]);
                }
            }
            return $this->render('catalogs_home/new.html.twig', [
                'caption' => 'Dodawanie katalogu',
                'error' => 'Nie dodano pliku',
                'form' => $form->createView(),
            ]);
        }
        return $this->render('catalogs_home/new.html.twig', [
            'caption' => 'Dodawanie katalogu',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/catalog/edit/{id}', name: 'app_edit_catalog')]
    public function editCatalog(Request $request, CatalogHandlingService $catalogHandlingService, CatalogRepository $catalogRepository, int $id, CheckObjectNameService $checkObjectNameService): Response
    {
        $catalog = $catalogRepository->find($id);
        if ($catalog) {
            $oldPdfFile = $catalog->getPdfFile();
            $oldName = strtolower($catalog->getName());
            $filesystem = new Filesystem();
            $form = $this->createForm(CatalogType::class, $catalog);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if($catalog->getDateAdded() <= new \DateTime()) {
                    if ($checkObjectNameService->checkIfCatalogExists($catalog->getName()) || strtolower($catalog->getName()) === $oldName) {
                        $catalog = $catalogHandlingService->createOfUpdateCatalog($catalog, $form);
                        $this->crudService->persistEntity($catalog);
                        if ($catalog->getPdfFile() !== $oldPdfFile) {

                            $olfPdfFilePath = $this->getParameter('catalogs_directory') . '/' . $oldPdfFile;
                            $filesystem->remove($olfPdfFilePath);
                        }
                        $this->logService->createLog(
                            explode('::', $request->attributes->get('_controller'))[1],
                            $catalog->getSystem()->getName() . ': ' . $catalog->getName()
                        );
                        $this->addFlash(
                            'success',
                            'Zmiany zostały zapisane'
                        );
                        return $this->redirectToRoute('app_catalogs_home');
                    } else {
                        return $this->render('catalogs_home/new.html.twig', [
                            'error' => 'Katalog o takiej nazwie już istnieje',
                            'caption' => 'Edycja katalogu',
                            'form' => $form->createView()
                        ]);
                    }
                } else {
                    return $this->render('catalogs_home/new.html.twig', [
                        'error' => 'Wprowadzono nieprawidłową datę',
                        'caption' => 'Edycja katalogu',
                        'form' => $form->createView()
                    ]);
                }
            }
            return $this->render('catalogs_home/new.html.twig', [

                'caption' => 'Edycja katalogu',
                'form' => $form->createView()
            ]);
        }
        return $this->render('error_page/index.html.twig', [
            'message' => 'Brak katalogu o podanym numerze id.'
        ]);
    }

    #[Route('/catalog/delete/{id}', name: 'app_delete_catalog')]
    public function deleteCatalog(Request $request, CatalogRepository $catalogRepository, CatalogHandlingService $catalogHandlingService, int $id): Response
    {
        $catalog = $catalogRepository->find($id);
        if (!$catalog) {
            return $this->render('error_page/index.html.twig', [
                'message' => 'Brak danych'
            ]);
        }
        if (!$catalogHandlingService->deleteCatalogFile($catalog)) {
            return $this->render('error_page/index.html.twig', [
                'message' => 'Błąd podczas usuwania: Nie znaleziono pliku'
            ]);
        }
        $this->crudService->deleteEntity($catalog);
        $this->logService->createLog(
            explode('::', $request->attributes->get('_controller'))[1],
            $catalog->getSystem()->getName() . ': ' . $catalog->getName()
        );
        $this->addFlash(
            'success',
            'Katalog został usunięty'
        );
        return $this->redirectToRoute('app_catalogs_home');
    }
}
