<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\RegisterRequestRepository;
use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class GetReportsListService
{
    private Security $security;
    private ReportRepository $reportRepository;
    public function __construct(Security $security, ReportRepository $reportRepository)
    {
        $this->security = $security;
        $this->reportRepository = $reportRepository;
    }

    public function getReportsList() : array
    {
        $currentUserRoles = $this->security->getUser()->getRoles();
        $allRolesOfCurrentUser = [];
        if(in_array('ROLE_GOD',$currentUserRoles))
        {
            $allRolesOfCurrentUser = ['ROLE_GOD', 'ROLE_ADMIN', 'ROLE_EDITOR'];
        } else if(in_array('ROLE_ADMIN', $currentUserRoles)) {
            $allRolesOfCurrentUser = ['ROLE_ADMIN', 'ROLE_EDITOR'];
        } else if(in_array('ROLE_EDITOR', $currentUserRoles)) {
            $allRolesOfCurrentUser = ['ROLE_EDITOR'];
        }
        $reports = $this->reportRepository->findAll();
        $reportsArray = [];
        foreach($reports as $report)
        {
            if(in_array($report->getCategory()->getRole(), $allRolesOfCurrentUser)) $reportsArray[] = $report;
        }
        return $reportsArray;
    }
}