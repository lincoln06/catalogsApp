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
    private UserRepository $userRepository;
    private ReportRepository $reportRepository;
    public function __construct(UserRepository $userRepository, Security $security, ReportRepository $reportRepository)
    {

        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->reportRepository = $reportRepository;
    }

    public function getReportsList() : array
    {
        $currentUserRoles = $this->security->getUser()->getRoles();
        $reports = $this->reportRepository->findAll();
        $reportsArray = [];
        foreach($reports as $report)
        {
            if(in_array($report->getCategory()->getRole(), $currentUserRoles)) $reportsArray[] = $report;
        }
        return $reportsArray;
    }
}