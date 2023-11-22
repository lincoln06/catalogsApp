<?php

namespace App\Services;

use App\Entity\ReportCategory;
use App\Repository\ReportRepository;

class ReportCategoryHandlingService
{
    private ReportRepository $reportRepository;
    private CRUDService $crudService;
    public function __construct(ReportRepository $reportRepository, CRUDService $crudService)
    {
        $this->reportRepository = $reportRepository;
        $this->crudService = $crudService;
    }
    public function deleteReportCategory(ReportCategory $reportCategory)
    {
        $reports = $reportCategory->getReports();
        foreach($reports as $report)
        {
            $this->crudService->deleteEntity($report);
        }
        $this->crudService->deleteEntity($reportCategory);
    }
}