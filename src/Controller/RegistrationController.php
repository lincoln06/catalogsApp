<?php

namespace App\Controller;

use App\Entity\RegisterRequest;
use App\Entity\User;
use App\Form\RegisterRequestType;
use App\Form\RegistrationFormType;
use App\Services\UserRegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function registrationRequest(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $hash = sha1(time());
        $session->set('hash', $hash);
        $registerRequest = new RegisterRequest();
        $form = $this->createForm(RegisterRequestType::class, $registerRequest);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $registerRequest->setEmail(
              $form->get('email')->getData()
            );
            $registerRequest->setHash($hash);
            $entityManager->persist($registerRequest);
            $entityManager->flush();
            return $this->redirectToRoute('app_catalogs_home');
        }
        return $this->render('registration/register_request.html.twig',[
           'form' => $form
        ]);
    }
    #[Route('/register/allowed/{commonHash}', name: 'app_register_allowed')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, string $commonHash, UserRegistrationService $userRegistrationService): Response
    {
        $registerRequest = $entityManager->getRepository(RegisterRequest::class)->findOneBy(['hash' => $commonHash]);
        $session = $request->getSession();
        $hash = $session->get('hash');
            if($hash !== $commonHash || !$registerRequest) {
                return $this->redirectToroute('app_access_denied');
            }
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $userRegistrationService->deleteRegisterRequest($registerRequest);
                return $this->redirectToRoute('app_catalogs_home');
            }
            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);


    }

}
