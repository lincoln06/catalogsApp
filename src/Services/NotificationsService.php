<?php

namespace App\Services;

use App\Repository\RegisterRequestRepository;

class NotificationsService
{
    protected RegisterRequestRepository $registerRequestRepository;
    protected GetReportsListService $getReportsListService;

    public function __construct(RegisterRequestRepository $registerRequestRepository, GetReportsListService $getReportsListService)
    {
        $this->registerRequestRepository = $registerRequestRepository;
        $this->getReportsListService = $getReportsListService;
    }

    public function getNotifications(): array
    {
        $numberOfRegisterRequests = count($this->registerRequestRepository->findAll());
        $numberOfReports = count($this->getReportsListService->getReportsList());
        return array(
            'numberOfRegisterRequests' => $numberOfRegisterRequests,
            'numberOfReports' => $numberOfReports,
        );
    }
}