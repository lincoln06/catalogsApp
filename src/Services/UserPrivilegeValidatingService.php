<?php

namespace App\Services;

use App\Entity\Report;
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
        if(in_array('ROLE_GOD', $user->getRoles())) return false;
        if(in_array('ROLE_ADMIN', $user->getRoles()) && in_array('ROLE_GOD', $currentUserRoles)) return true;
        if(in_array('ROLE_ADMIN', $user->getRoles()) && in_array('ROLE_ADMIN', $currentUserRoles)) return false;
        return true;
    }
    public function checkManageReportPrivileges(Report $report): bool
    {
        $currentUserRoles = $this->security->getUser()->getRoles();
        $role = $report->getCategory()->getRole();
        switch($role)
        {
            case 'ROLE_GOD':
                if(in_array('ROLE_GOD', $currentUserRoles)) return true;
                break;
            case 'ROLE_ADMIN':
                if(in_array('ROLE_GOD', $currentUserRoles) || in_array('ROLE_ADMIN', $currentUserRoles)) return true;
                break;
            case 'ROLE_EDITOR':
                if(in_array('ROLE_GOD', $currentUserRoles) || in_array('ROLE_ADMIN', $currentUserRoles) || in_array('ROLE_EDITOR', $currentUserRoles)) return true;
                break;
        }
        return false;
    }
}