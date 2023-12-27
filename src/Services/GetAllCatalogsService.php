<?php

namespace App\Services;

use App\Repository\SystemRepository;

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
            if (count($system->getCatalogs()) !== 0) {
                $catalogs[$system->getName()] = $system->getCatalogs();
            }
        }
        return $catalogs;
    }

    public function getAllSystems(): array
    {
        return $this->systemRepository->findBy(array(), array('name' => 'ASC'));

    }

    public function getSystemsWithCatalogs(): array
    {
        $systems = [];
        $allSystems = $this->systemRepository->findBy(array(), array('name' => 'ASC'));
        foreach ($allSystems as $system) {
            if (count($system->getCatalogs()) !== 0) {
                $systems[] = $system;
            }
        }
        return $systems;
    }
}