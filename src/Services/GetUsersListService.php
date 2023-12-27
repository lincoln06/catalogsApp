<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\RegisterRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class GetUsersListService
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private RegisterRequestRepository $registerRequestRepository;

    public function __construct(UserRepository $userRepository, Security $security, RegisterRequestRepository $registerRequestRepository, EntityManagerInterface $entityManager)
    {

        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->registerRequestRepository = $registerRequestRepository;
        $this->entityManager = $entityManager;
    }

    public function getUsersFromDatabase(): array
    {
        $listOfUsersToExport = [];
        $listOfAllUsers = $this->userRepository->findBy(array(), array('email' => 'ASC'));
        foreach ($listOfAllUsers as $user) {
            if ($this->security->isGranted('ROLE_GOD')) {
                if (!in_array('ROLE_GOD', $user->getRoles())) $listOfUsersToExport[] = $user;
            } elseif ($this->security->isGranted('ROLE_ADMIN')) {
                if (!(in_array('ROLE_GOD', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles()))) {
                    $listOfUsersToExport[] = $user;
                }
            }
        }
        return $listOfUsersToExport;
    }

    public function checkIfIsEmailRegistered(string $email): bool
    {
        $isRegistered = $this->userRepository->findBy(['email' => $email]);
        if (!$isRegistered) {
            $isThereRequestForThisEmail = $this->registerRequestRepository->findOneBy(['email' => $email]);
            if ($isThereRequestForThisEmail) {
                $this->entityManager->remove($isThereRequestForThisEmail);
                $this->entityManager->flush();
            }
            return false;
        }
        return true;
    }

    public function getRegisteredEmails(): array
    {
        $registeredUsers = $this->userRepository->findAll();
        $emailsArray = [];
        foreach ($registeredUsers as $registeredUser) {
            $emailsArray[] = $registeredUser->getEmail();
        }
        return $emailsArray;
    }

    private function checkFetchedUserRoles(string $roleToCheck, User $user): bool
    {
        $roles = $user->getRoles();
        if (in_array($roleToCheck, $roles) || in_array('ROLE_GOD', $roles)) {
            return true;
        }
        return false;
    }
}