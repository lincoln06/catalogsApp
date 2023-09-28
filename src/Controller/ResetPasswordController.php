<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterRequestType;
use App\Form\ResetPasswordType;
use App\Services\GetUsersListService;
use App\Services\HashSetter;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class ResetPasswordController extends AbstractController
{

    #[Route('/reset/password', name: 'app_forgot_password_request')]
    public function request(Request $request, GetUsersListService $getGetUsersListService, MailerService $mailerService, HashSetter $hashSetter): Response
    {
        $form = $this->createForm(RegisterRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $registeredEmails = $getGetUsersListService->getRegisteredEmails();
            if(!in_array($email, $registeredEmails))
            {
                return $this->render('registration/register_request.html.twig', [
                    'form' => $form->createView(),
                    'message' => 'Brak użytkownika o podanym adresie e-mail'
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
                "Aby zresetować hasło, skopiuj ten link do przeglądarki: localhost:8000/reset/password/$hash"
            );
            return $this->render('registration/register_request.html.twig', [
                'form' => $form->createView(),
                'message' => $message
            ]);
        }

        return $this->render('registration/register_request.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/reset/password/{commonHash}', name: 'app_reset_password_')]
    public function reset(Request $request, GetUsersListService $getGetUsersListService, string $commonHash, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $session = $request->getSession();
        $hash = $session->get('hash');
        if(!$hash || ($commonHash !== $hash)) {
            return $this->render('error_page/index.html.twig', [
               'message' => 'Link wygasł lub nie masz uprawnień do przeglądania tej strony'
            ]);
        }
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $emailToCheck = $session->get('email');
            if($email !== $emailToCheck)
            {
                return $this->render('error_page/index.html.twig', [
                    'message' => 'Link wygasł lub nie masz uprawnień do przeglądania tej strony'
                ]);
            }
            $password = $form->get('password')->getData();
            $repeatedPassword = $form->get('repeatPassword')->getData();
            $registeredEmails = $getGetUsersListService->getRegisteredEmails();
            if(!in_array($email, $registeredEmails))
            {
                return $this->render('reset_password/index.html.twig', [
                    'resetForm' => $form->createView(),
                    'message' => 'Brak użytkownika o podanym adresie e-mail'
                ]);
            }
            if($password !== $repeatedPassword) {
                return $this->render('reset_password/index.html.twig', [
                    'resetForm' => $form->createView(),
                    'message' => 'Hasła muszą być takie same'
                ]);
            }
            $usersRepository = $entityManager->getRepository(User::class);
            $user = $usersRepository->findOneBy(['email' => $email]);
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->render('reset_password/done.html.twig');

        }

        return $this->render('reset_password/index.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
