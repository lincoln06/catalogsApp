<?php

namespace App\Services;

use App\Entity\Catalog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class CatalogHandlingService extends AbstractController
{
    private SluggerInterface $slugger;
    private EntityManagerInterface $entityManager;
    public function __construct(SluggerInterface $slugger, EntityManagerInterface $entityManager) {
        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
    }
    public function createOfUpdateCatalog(Catalog $catalog, FormInterface $form) : ?Catalog
    {
        $catalog->setSystem($form->get('system')->getData());
        $catalog->setName($form->get('name')->getData());
        $catalog->setDateAdded($form->get('dateAdded')->getData());
        $pdfFile = $form->get('pdfFile')->getData();
        if ($pdfFile) {
            $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();
            $catalog->setPdfFile($newFilename);

            try {
                $pdfFile->move(
                    $this->getParameter('catalogs_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            return $catalog;
        }
        else {
            if($catalog->getPdfFile() !== null) return $catalog;
        }
        return null;
    }
    public function deleteCatalogFile(Catalog $catalog) : bool
    {
        $pdfFile = $catalog->getPdfFile();
        $filesystem = new Filesystem();
        if($pdfFile) {
            $pdfFilePath = $this->getParameter('catalogs_directory') . '/' . $pdfFile;
            $filesystem->remove($pdfFilePath);
            return true;
        }
        return false;
    }
}