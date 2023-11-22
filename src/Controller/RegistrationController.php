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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends MainController
{
    #[Route('/register', name: 'app_register')]
    public function registrationRequest(Request $request, GetUsersListService $getUsersListService, HashSetterService $hashSetter): Response
    {
        $session = $request->getSession();

        $registerRequest = new RegisterRequest();
        $form = $this->createForm(RegisterRequestType::class, $registerRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emailToCheck = $form->get('email')->getData();
            $isEmailRegistered = $getUsersListService->checkIfIsEmailRegistered($emailToCheck);
            $email = $form->get('email')->getData();
            $hash = $hashSetter->makeHash();
            $session->set('hash', $hash);
            if ($isEmailRegistered) {
                return $this->render('registration/register_request.html.twig', [
                    'message' => 'Na ten adres e-mail już zostało utworzone konto lub zostało wysłane zapytanie',
                    'form' => $form
                ]);
            }
            $registerRequest->setEmail($email);
            $registerRequest->setHash($hash);
            $this->crudService->persistEntity($registerRequest);
            return $this->render('registration/register_request.html.twig', [
                'message' => 'Prośba została wysłana',
                'form' => $form
            ]);
        }
        return $this->render('registration/register_request.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/register/allowed/{commonHash}', name: 'app_register_allowed')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, RegisterRequestRepository $registerRequestRepository, string $commonHash, UserRegistrationService $userRegistrationService): Response
    {
        $registerRequest = $registerRequestRepository->findOneBy(['hash' => $commonHash]);
        $session = $request->getSession();
        $hash = $session->get('hash');
        if ($hash !== $commonHash || !$registerRequest) {
            return $this->redirectToroute('app_access_denied');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            if ($email !== $registerRequest->getEmail()) {
                return $this->render('error_page/index.html.twig', [
                    'message' => 'Ten adres e-mail nie istnieje w systemie'
                ]);
            }
            $password = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));

            $this->crudService->persistEntity($user);
            $session->remove('hash');
            $userRegistrationService->deleteRegisterRequest($registerRequest);
            return $this->redirectToRoute('app_catalogs_home');
        }
        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
