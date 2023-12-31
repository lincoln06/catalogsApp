<?php

namespace App\Controller;

use App\Entity\RegisterRequest;
use App\Entity\User;
use App\Form\RegisterRequestType;
use App\Form\RegistrationFormType;
use App\Repository\RegisterRequestRepository;
use App\Services\GetUsersListService;
use App\Services\HashSetterService;
use App\Services\UserRegistrationService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends MainController
{
    #[Route('/register', name: 'app_register')]
    public function registrationRequest(Request $request, GetUsersListService $getUsersListService, HashSetterService $hashSetter): Response
    {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_catalogs_home');
        }
        $registerRequest = new RegisterRequest();
        $form = $this->createForm(RegisterRequestType::class, $registerRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $email = trim(strtolower($email));
            $isEmailRegistered = $getUsersListService->checkIfIsEmailRegistered($email);
            $hash = $hashSetter->makeHash();
            if ($isEmailRegistered) {
                return $this->render('registration/register_request.html.twig', [
                    'message' => 'Na ten adres e-mail już zostało utworzone konto lub zostało wysłane zapytanie',
                    'form' => $form,
                    'caption' => 'Prośba o dostęp'
                ]);
            }
            $registerRequest->setEmail(trim(strtolower($email)));
            $registerRequest->setHash($hash);
            $this->crudService->persistEntity($registerRequest);
            return $this->render('registration/register_request.html.twig', [
                'message' => 'Prośba została wysłana',
                'form' => $form,
                'caption' => 'Prośba o dostęp'
            ]);
        }
        return $this->render('registration/register_request.html.twig', [
            'caption' => 'Prośba o dostęp',
            'form' => $form
        ]);
    }

    #[Route('/register/allowed/{commonHash}', name: 'app_register_allowed')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, RegisterRequestRepository $registerRequestRepository, string $commonHash, UserRegistrationService $userRegistrationService): Response
    {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_catalogs_home');
        }
        $registerRequest = $registerRequestRepository->findOneBy(['hash' => $commonHash]);
        if (!$registerRequest) {
            return $this->redirectToroute('app_access_denied');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $email = trim(strtolower($email));
            if ($email !== $registerRequest->getEmail()) {
                return $this->render('error_page/index.html.twig', [
                    'message' => 'Ten adres e-mail nie istnieje w systemie'
                ]);
            }
            $password = $form->get('plainPassword')->getData();
            $user->setEmail($email);
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));

            $this->crudService->persistEntity($user);

            $userRegistrationService->deleteRegisterRequest($registerRequest);
            return $this->redirectToRoute('app_catalogs_home');
        }
        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
