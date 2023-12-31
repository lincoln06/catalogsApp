<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterRequestType;
use App\Form\ResetPasswordType;
use App\Services\GetUsersListService;
use App\Services\HashSetterService;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class ResetPasswordController extends MainController
{

    #[Route('/reset/password', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerService $mailerService, GetUsersListService $getUsersListService, HashSetterService $hashSetter): Response
    {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_catalogs_home');
        }
        $form = $this->createForm(RegisterRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $email = trim(strtolower($email));
            $registeredEmails = $getUsersListService->getRegisteredEmails();
            if (!in_array($email, $registeredEmails)) {
                return $this->render('registration/register_request.html.twig', [
                    'form' => $form->createView(),
                    'message' => 'Brak użytkownika o podanym adresie e-mail',
                    'caption' => 'Resetowanie hasła'
                ]);
            }
            $session = $request->getSession();
            $hash = $hashSetter->makeHash();
            $session->set('hash', $hash);
            $session->set('email', $email);
            $message = "Sprawdź swoją skrzynkę odbiorczą";
            $mailerService->sendEmail(
                $email,
                "Katalogi GABIT - Link do resetowania hasła",
                "Aby zresetować hasło, skopiuj ten link do przeglądarki: \n\n",
                "/reset/password/",
                $hash
            );
            return $this->render('registration/register_request.html.twig', [
                'form' => $form->createView(),
                'message' => $message,
                'caption' => 'Resetowanie hasła'
            ]);
        }

        return $this->render('registration/register_request.html.twig', [
            'caption' => 'Resetowanie hasła',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset/password/{commonHash}', name: 'app_reset_password_')]
    public function reset(Request $request, GetUsersListService $getGetUsersListService, string $commonHash, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $session = $request->getSession();
        $hash = $session->get('hash');
        if (!$hash || ($commonHash !== $hash)) {
            return $this->render('error_page/index.html.twig', [
                'message' => 'Link wygasł lub nie masz uprawnień do przeglądania tej strony'
            ]);
        }
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $email = trim(strtolower($email));
            $emailToCheck = $session->get('email');
            if ($email !== $emailToCheck) {
                return $this->render('error_page/index.html.twig', [
                    'message' => 'Link wygasł lub nie masz uprawnień do przeglądania tej strony'
                ]);
            }
            $password = $form->get('password')->getData();
            $repeatedPassword = $form->get('repeatPassword')->getData();
            $registeredEmails = $getGetUsersListService->getRegisteredEmails();
            if (!in_array($email, $registeredEmails)) {
                return $this->render('reset_password/index.html.twig', [
                    'form' => $form->createView(),
                    'message' => 'Brak użytkownika o podanym adresie e-mail'
                ]);
            }
            if ($password !== $repeatedPassword) {
                return $this->render('reset_password/index.html.twig', [
                    'form' => $form->createView(),
                    'message' => 'Hasła muszą być takie same',
                    'caption' => 'Resetowanie hasła'
                ]);
            }
            $usersRepository = $entityManager->getRepository(User::class);
            $user = $usersRepository->findOneBy(['email' => $email]);
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            $session->remove('hash');
            $session->remove('email');
            $this->crudService->persistEntity($user);
            return $this->render('reset_password/done.html.twig');
        }

        return $this->render('reset_password/index.html.twig', [
            'form' => $form->createView(),
            'caption' => 'Resetowanie hasła'
        ]);
    }
}
