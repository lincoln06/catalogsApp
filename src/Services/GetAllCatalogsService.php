<?php

namespace App\Services;

use App\Repository\SystemRepository;
use PhpParser\Node\Expr\Array_;

class GetAllCatalogsService
{
    private SystemRepository $systemRepository;

    public function __construct(SystemRepository $systemRepository)
    {
        $this->systemRepository = $systemRepository;
    }
    public function getAllCatalogs(): array
    {
        $systems = $this->getAllSystems();
        $catalogs = [];
        foreach ($systems as $system) {
            $catalogs[$system->getName()] = $system->getCatalogs();
        }
        return $catalogs;
    }
    public function getAllSystems(): array
    {
        return $this->systemRepository->findAll();
    }
}