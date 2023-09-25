<?php

namespace App\Services;

use App\Entity\RegisterRequest;
use App\Entity\User;
use App\Repository\RegisterRequestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class GetUsersListService
{
    private Security $security;
    private UserRepository $userRepository;
    private RegisterRequestRepository $registerRequestRepository;
    public function __construct(UserRepository $userRepository, Security $security, RegisterRequestRepository $registerRequestRepository)
    {

        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->registerRequestRepository = $registerRequestRepository;
    }

    public function getUsersFromDatabase() : array
    {

        $currentUserRoles = $this->security->getUser()->getRoles();
        $currentUserRole = '';
        foreach($currentUserRoles as $role) {
            if($role === 'ROLE_ADMIN' || $role ==='ROLE_GOD') {
                $currentUserRole = $role;
                break;
            }
        }
        $usersList = $this->userRepository->findAll();
        foreach ($usersList as $user) {
            if($this->checkFetchedUserRoles($currentUserRole, $user)) {
                $index = array_search($user, $usersList);
                if($index !== false) {
                    unset($usersList[$index]);
                }
            };
        }
        return $usersList;
    }
    public function getRegisteredEmails() : array
    {
        $usersList = $this->userRepository->findAll();
        $registerRequestsList = $this->registerRequestRepository->findAll();
        $emailsArray = [];
        forEach($usersList as $user)
        {
            $emailsArray[] =  $user->getEmail();
        }
        forEach($registerRequestsList as $registerRequest)
        {
            $emailsArray[] = $registerRequest->getEmail();
        }
        return $emailsArray;
    }
    private function checkFetchedUserRoles(string $roleToCheck, User $user): bool {
        $roles = $user->getRoles();
        if (in_array($roleToCheck, $roles) || in_array('ROLE_GOD', $roles)) {
            return true;
        }
        return false;
    }
}