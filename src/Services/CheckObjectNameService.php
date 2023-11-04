<?php

namespace App\Services;

use App\Repository\SystemRepository;

class CheckObjectNameService
{
    private SystemRepository $systemRepository;

    public function __construct(SystemRepository $systemRepository)
    {
        $this->systemRepository = $systemRepository;
    }

    public function checkIfSystemExists(string $newSystemName): bool
    {
        $system = $this->systemRepository->findOneBy(['name' => $newSystemName]);
        if ($system) return false;
        return true;
    }
}