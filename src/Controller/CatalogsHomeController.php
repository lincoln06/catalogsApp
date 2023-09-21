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

class CatalogsHomeController extends AbstractController
{
    #[Route('/', name: 'app_catalogs_home')]
    public function index(SystemRepository $systemRepository): Response
    {
        $message = '';
//        $catalogs = $entityManager->getRepository(Catalog::class)->findAll();
//        if(!$catalogs) $message = 'Nic do wyświetlenia';
//        return $this->render('catalogs_home/index.html.twig', [
//            'message' => $message,
//            'catalogs' => $catalogs
//        ]);
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
    #[Route('/catalog/add', name: 'app_catalogs_add')]
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

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pdfFile->move(
                        $this->getParameter('catalogs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
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
        $message = '';
        if(!$catalog) {
            $message = 'Brak danych';
        }
        $form = $this -> createForm(CatalogType::class, $catalog);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $catalog->setSystem($form->get('system')->getData());
            $catalog->setName($form->get('name')->getData());
            $catalog->setDateAdded($form->get('dateAdded')->getData());
            $pdfFile = $form->get('pdfFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pdfFile->move(
                        $this->getParameter('catalogs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $catalog->setPdfFile($newFilename);
            }

            $entityManager->persist($catalog);
            $entityManager->flush();
            return $this -> redirectToRoute('app_catalogs_home');
        }
        return $this->render('add_catalog/index.html.twig', [
            'caption' => 'Edytuj Katalog',
            'form' => $form->createView(),
            'message' => $message
        ]);
    }
}
