<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class CRUDService
{
    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function deleteEntity($entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function persistEntity($entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}