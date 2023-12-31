<?php

namespace App\Services;

use App\Repository\CatalogRepository;
use App\Repository\SystemRepository;

class CheckObjectNameService
{
    private SystemRepository $systemRepository;
    private CatalogRepository $catalogRepository;

    public function __construct(SystemRepository $systemRepository, CatalogRepository $catalogRepository)
    {
        $this->systemRepository = $systemRepository;
        $this->catalogRepository = $catalogRepository;
    }

    public function checkIfSystemExists(string $newSystemName): bool
    {
        $system = $this->systemRepository->findOneBy(['name' => $newSystemName]);
        if ($system) return false;
        return true;
    }
    public function checkIfCatalogExists (string $newCatalogName): bool
    {
        $catalog = $this->catalogRepository->findOneBy(['name' => $newCatalogName]);
        if($catalog) return false;
        return true;
    }
}