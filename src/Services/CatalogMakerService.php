<?php

namespace App\Services;

use App\Entity\Catalog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class CatalogMakerService extends AbstractController
{
    private SluggerInterface $slugger;
    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }
    public function createOfUpdateCatalog(Catalog $catalog, FormInterface $form) : Catalog
    {
        $catalog->setSystem($form->get('system')->getData());
        $catalog->setName($form->get('name')->getData());
        $catalog->setDateAdded($form->get('dateAdded')->getData());
        $pdfFile = $form->get('pdfFile')->getData();

        if ($pdfFile) {
            $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

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
        return $catalog;
    }
}