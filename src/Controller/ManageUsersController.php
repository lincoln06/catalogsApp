<?php

namespace App\Controller;

use App\Form\EditUserType;
use App\Repository\RegisterRequestRepository;
use App\Repository\UserRepository;
use App\Services\GetUsersListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageUsersController extends MainController
{
    #[Route('/manage/users', name: 'app_manage_users')]
    public function index(GetUsersListService $getUsersListService): Response
    {
        return $this->render('manage_users/index.html.twig', [
            'usersList' => $getUsersListService->getUsersFromDatabase(),
        ]);
    }
    #[Route('/manage/users/delete/{id}', name: 'app_delete_user')]
    public function delete(UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->render('error_page/index.html.twig', [
                'message' => 'Nieprawidłowy id użytkownika',
                ]);
        }
        $this->crudService->deleteEntity($user);
        return $this->redirectToRoute('app_manage_users');
    }
    #[Route('/manage/users/edit/{id}', name: 'app_edit_user')]
    public function edit(Request $request, UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);
        $form = $this -> createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setRoles($data->getRoles());
            $this->crudService->persistEntity($user);
            return $this -> redirectToRoute('app_manage_users');
        }
        return $this->render('manage_users/edit.html.twig', [
            'form' => $form,
            'username' => $user->getEmail()
        ]);
    }
    #[Route('/manage/requests', name: 'app_manage_requests')]
    public function requests(RegisterRequestRepository $registerRequestRepository): Response
    {
        $message = "Lista zapytań o rejestrację";
        $requests = $registerRequestRepository->findAll();
        if(!$requests) $message = "Brak zapytań o rejestrację";
        return $this->render('manage_users/requests.html.twig', [
            'message' =>$message,
            'requests' => $requests,
        ]);
    }
    #[Route('/manage/requests/confirm/{id}', name: 'app_request_confirm')]
    public function confirm(RegisterRequestRepository $registerRequestRepository, $userRegistrationService, int $id): Response
    {
        $registerRequest = $registerRequestRepository->find($id);
        $registerRequest->setIsAccepted(true);
        $this->crudService->persistEntity($registerRequest);
        $userRegistrationService->allowToRegister($registerRequest);
        return $this->redirectToRoute('app_manage_requests');
    }
    #[Route('/manage/requests/deny/{id}', name: 'app_request_deny')]
    public function deny(RegisterRequestRepository $registerRequestRepository, $userRegistrationService, int $id): Response
    {
        $registerRequest = $registerRequestRepository->find($id);
        $userRegistrationService->deleteRegisterRequest($registerRequest);
        return $this->redirectToRoute('app_manage_requests');
    }
}
