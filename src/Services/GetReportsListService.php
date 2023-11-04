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

    public function getReportsList(): array
    {
        $reports = $this->reportRepository->findAll();
        $reportsArray = [];
        foreach ($reports as $report) {
            if ($this->security->isGranted($report->getCategory()->getRole())) {
                $reportsArray[] = $report;
            }
        }
        return $reportsArray;
    }
}