<?php

namespace App\Controller;

use App\Entity\Catalog;
use App\Form\CatalogType;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class CatalogsHomeController extends AbstractController
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
            ]
        );
    }
    #[Route('/catalog/add', name: 'app_add_catalog')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $catalog = new Catalog();

        $form = $this -> createForm(CatalogType::class, $catalog);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $catalog->setSystem($form->get('system')->getData());
            $catalog->setName($form->get('name')->getData());
            $catalog->setDateAdded($form->get('dateAdded')->getData());
            $pdfFile = $form->get('pdfFile')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('catalogs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $catalog->setPdfFile($newFilename);
            }

            $entityManager->persist($catalog);
            $entityManager->flush();
            return $this -> redirectToRoute('app_catalogs_home');
        }
        return $this->render('catalogs_home/new.html.twig', [
            'caption' => 'Dodaj katalog',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/catalog/edit/{id}', name: 'app_edit_catalog')]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, int $id): Response
    {
        $catalog = $entityManager->getRepository(Catalog::class)->find($id);
        if(!$catalog) {
            $message = 'Brak danych';
        }
        $oldPdfFile = $catalog->getPdfFile();
        $message = '';
        $filesystem = new Filesystem();

        $form = $this -> createForm(CatalogType::class, $catalog);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $catalog->setSystem($form->get('system')->getData());
            $catalog->setName($form->get('name')->getData());
            $catalog->setDateAdded($form->get('dateAdded')->getData());
            $pdfFile = $form->get('pdfFile')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('catalogs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $catalog->setPdfFile($newFilename);
            }

            $entityManager->persist($catalog);
            $entityManager->flush();
            if($oldPdfFile) {
                $olfPdfFilePath = $this->getParameter('catalogs_directory') . '/' . $oldPdfFile;

                $filesystem->remove($olfPdfFilePath);
            }
            return $this -> redirectToRoute('app_catalogs_home');
        }
        return $this->render('add_catalog/index.html.twig', [
            'caption' => 'Edytuj Katalog',
            'form' => $form->createView(),
            'message' => $message
        ]);
    }
    #[Route('/catalog/delete/{id}', name: 'app_delete_catalog')]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $catalog = $entityManager->getRepository(Catalog::class)->find($id);
        if(!$catalog) {
            $message = 'Brak danych';
        }
        $pdfFile = $catalog->getPdfFile();
        $filesystem = new Filesystem();
        if($pdfFile) {
            $pdfFilePath = $this->getParameter('catalogs_directory') . '/' . $pdfFile;

            $filesystem->remove($pdfFilePath);
        }
        $message = '';

        $entityManager->remove($catalog);
        $entityManager->flush();
        return $this->redirectToRoute('app_catalogs_home');
    }
}
