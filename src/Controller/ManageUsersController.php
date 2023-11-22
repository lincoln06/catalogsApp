<?php

namespace App\Controller;

use App\Form\EditUserType;
use App\Repository\RegisterRequestRepository;
use App\Repository\UserRepository;
use App\Services\CRUDService;
use App\Services\GetUsersListService;
use App\Services\LogService;
use App\Services\UserPrivilegeValidatingService;
use App\Services\UserRegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageUsersController extends MainController
{
    private UserPrivilegeValidatingService $userPrivilegeValidatingService;

    public function __construct(UserPrivilegeValidatingService $userPrivilegeValidatingService, EntityManagerInterface $entityManager, CRUDService $crudService, LogService $logService)
    {
        parent::__construct($entityManager, $crudService, $logService);
        $this->userPrivilegeValidatingService = $userPrivilegeValidatingService;
    }

    #[Route('/manage/users', name: 'app_manage_users')]
    public function index(GetUsersListService $getUsersListService): Response
    {
        return $this->render('manage_users/index.html.twig', [
            'caption' => 'Lista użytkowników',
            'usersList' => $getUsersListService->getUsersFromDatabase(),
        ]);
    }

    #[Route('manage/users/delete/{id}', name: 'app_delete_user')]
    public function deleteUser(Request $request, UserRepository $userRepository, int $id, GetUsersListService $getUsersListService): Response
    {
        $message = '';
        $user = $userRepository->find($id);

        if (!$user) {
            $message = 'Nieprawidłowy id użytkownika';
        } elseif (!$this->userPrivilegeValidatingService->checkManageUsersPrivileges($user)) {
            $message = 'Nie masz uprawnień do wykonania tej akcji';
        } else {
            $this->crudService->deleteEntity($user);
            $this->logService->createLog(
                explode('::', $request->attributes->get('_controller'))[1],
                $user->getEmail()
            );
            return $this->render('manage_users/index.html.twig', [
                'caption' => 'Lista użytkowników',
                'message' => 'Użytkownik został usunięty',
                'usersList' => $getUsersListService->getUsersFromDatabase(),
            ]);
        }
        return $this->render('error_page/index.html.twig', [
            'message' => $message,
        ]);

    }

    #[Route('/manage/users/edit/{id}', name: 'app_edit_user')]
    public function editUser(Request $request, UserRepository $userRepository, int $id, GetUsersListService $getUsersListService): Response
    {
        $user = $userRepository->find($id);
        $message = '';
        if (!$user) {
            $message = 'Nieprawidłowy id użytkownika';
        } elseif ($this->userPrivilegeValidatingService->checkManageUsersPrivileges($user) === false) {
            $message = 'Nie masz uprawnień do wykonania tej akcji';
        } else {
            $form = $this->createForm(EditUserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $user->setRoles($data->getRoles());
                $this->crudService->persistEntity($user);
                $this->logService->createLog(
                    explode('::', $request->attributes->get('_controller'))[1],
                    $user->getEmail()
                );
                return $this->render('manage_users/index.html.twig', [
                    'caption' => 'Lista użytkowników',
                    'message' => 'Zmiany zostały zapisane',
                    'usersList' => $getUsersListService->getUsersFromDatabase(),
                ]);
            }
            return $this->render('manage_users/edit.html.twig', [
                'caption' => 'Edycja użytkownika',
                'form' => $form,
                'username' => $user->getEmail()
            ]);
        }
        return $this->render('error_page/index.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/manage/requests', name: 'app_manage_requests')]
    public function requests(RegisterRequestRepository $registerRequestRepository): Response
    {
        $requests = $registerRequestRepository->findAll();
        return $this->render('manage_users/requests.html.twig', [
            'requests' => $requests,
        ]);
    }

    #[Route('/manage/requests/confirm/{id}', name: 'app_request_confirm')]
    public function confirmRegistration(Request $request, RegisterRequestRepository $registerRequestRepository, UserRegistrationService $userRegistrationService, int $id): Response
    {
        $registerRequest = $registerRequestRepository->find($id);
        $registerRequest->setIsAccepted(true);
        $this->crudService->persistEntity($registerRequest);$this->logService->createLog(
        explode('::', $request->attributes->get('_controller'))[1],
        $registerRequest->getEmail()
    );
        $userRegistrationService->allowToRegister($registerRequest);
        return $this->render('manage_users/requests.html.twig', [
            'requests' => $registerRequestRepository->findAll(),
            'message' => 'Wysłano link aktywacyjny'
        ]);
    }

    #[Route('/manage/requests/deny/{id}', name: 'app_request_deny')]
    public function denyRegistration(Request $request, RegisterRequestRepository $registerRequestRepository, UserRegistrationService $userRegistrationService, int $id): Response
    {
        $registerRequest = $registerRequestRepository->find($id);
        $userRegistrationService->deleteRegisterRequest($registerRequest);
        $this->logService->createLog(
        explode('::', $request->attributes->get('_controller'))[1],
        $registerRequest->getEmail()
    );
        return $this->render('manage_users/requests.html.twig', [
        'requests' => $registerRequestRepository->findAll(),
        'message' => 'Zapytanie zostało usunięte'
    ]);
    }
}
