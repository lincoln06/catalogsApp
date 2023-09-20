<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class GetUsersListService
{
    private Security $security;
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
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
    private function checkFetchedUserRoles(string $roleToCheck, User $user): bool {
        $roles = $user->getRoles();
        if (in_array($roleToCheck, $roles) || in_array('ROLE_GOD', $roles)) {
            return true;
        }
        return false;
    }
}