<?php

namespace App\Controller;

use App\Form\SystemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\System;

class SystemController extends AbstractController
{

    #[Route('/system/show', name: 'app_show_system')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $message = '';
        $systems = $entityManager->getRepository(System::class)->findAll();
        if(!$systems) {
            $message = 'Nic do wyświetlenia';
        }
        return $this->render('add_catalog/list.html.twig', [
            'systems' => $systems,
            'message' => $message
        ]);
    }
    #[Route('/system/add', name: 'app_add_system')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $system = new System();

        $form = $this -> createForm(SystemType::class, $system);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $system->setName($form->get('name')->getData());
            $entityManager->persist($system);
            $entityManager->flush();
            return $this -> redirectToRoute('app_show_system');
        }
        return $this->render('add_catalog/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/system/edit/{id}', name: 'app_edit_system')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $system = $entityManager->getRepository(System::class)->find($id);
        $message = '';
        if(!$system) {
            $message = 'Brak danych';
        }
        $form = $this -> createForm(SystemType::class, $system);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $system->setName($data->getName());
            $entityManager->persist($system);
            $entityManager->flush();
            return $this -> redirectToRoute('app_show_system');
        }
        return $this->render('add_catalog/index.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }
    #[Route('/system/delete/{id}', name: 'app_delete_system')]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $system = $entityManager->getRepository(System::class)->find($id);
        $message = '';
        if(!$system) {
            $message = 'Brak danych';
        }
        $entityManager->remove($system);
        $entityManager->flush();
        return $this->redirectToRoute('app_show_system');
    }
}
