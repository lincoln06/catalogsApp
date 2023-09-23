<?php

namespace App\Controller;

use App\Entity\RegisterRequest;
use App\Entity\User;
use App\Form\EditUserType;
use App\Services\GetUsersListService;
use App\Services\UserRegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageUsersController extends AbstractController
{
    #[Route('/manage/users', name: 'app_manage_users')]
    public function index(GetUsersListService $getUsersListService): Response
    {
        return $this->render('manage_users/index.html.twig', [
            'usersList' => $getUsersListService->getUsersFromDatabase(),
        ]);
    }
    #[Route('/manage/users/delete/{id}', name: 'app_delete_user')]
    public function delete(EntityManagerInterface $entityManager, int $id, GetUsersListService $getUsersListService): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $message = '';
        if (!$user) {
            $message = 'Brak danych';
        }
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_manage_users');
    }
    #[Route('/manage/users/edit/{id}', name: 'app_edit_user')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $message = '';
        if(!$user) {
            $message = 'Brak danych';
        }
        $form = $this -> createForm(EditUserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setRoles($data->getRoles());
            $entityManager->persist($user);
            $entityManager->flush();
            return $this -> redirectToRoute('app_manage_users');
        }
        return $this->render('manage_users/edit.html.twig', [
            'form' => $form,
            'username' => $user->getEmail()
        ]);

    }
    #[Route('/manage/requests', name: 'app_manage_requests')]
    public function requests(EntityManagerInterface $entityManager): Response
    {
        $message = "Lista zapytań o rejestrację";
        $requests = $entityManager->getRepository(RegisterRequest::class)->findAll();
        if(!$requests) $message = "Brak zapytań o rejestrację";
        return $this->render('manage_users/requests.html.twig', [
            'message' =>$message,
            'requests' => $requests,
        ]);
    }
    #[Route('/manage/requests/confirm/{id}', name: 'app_request_confirm')]
    public function confirm(Request $request, EntityManagerInterface $entityManager, UserRegistrationService $userRegistrationService, int $id): Response
    {
        $registerRequest = $entityManager->getRepository(RegisterRequest::class)->find($id);
        $userRegistrationService->allowToRegister($registerRequest);
        return $this->redirectToRoute('app_manage_requests');
    }
    #[Route('/manage/requests/deny/{id}', name: 'app_request_deny')]
    public function deny(Request $request, EntityManagerInterface $entityManager, UserRegistrationService $userRegistrationService, int $id): Response
    {
        $registerRequest = $entityManager->getRepository(RegisterRequest::class)->find($id);
        $userRegistrationService->deleteRegisterRequest($registerRequest);
        return $this->redirectToRoute('app_manage_requests');
    }
}
