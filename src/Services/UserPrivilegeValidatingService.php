<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class UserPrivilegeValidatingService
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function checkManageUsersPrivileges(User $user): bool
    {
        $currentUserRoles = $this->security->getUser()->getRoles();
        if (in_array('ROLE_GOD', $user->getRoles())) return false;
        if (in_array('ROLE_ADMIN', $user->getRoles()) && in_array('ROLE_GOD', $currentUserRoles)) return true;
        if (in_array('ROLE_ADMIN', $user->getRoles()) && in_array('ROLE_ADMIN', $currentUserRoles)) return false;
        return true;
    }
}