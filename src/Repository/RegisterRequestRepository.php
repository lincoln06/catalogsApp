<?php

namespace App\Repository;

use App\Entity\RegisterRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegisterRequest>
 *
 * @method RegisterRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegisterRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegisterRequest[]    findAll()
 * @method RegisterRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegisterRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegisterRequest::class);
    }

//    /**
//     * @return RegisterRequest[] Returns an array of RegisterRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RegisterRequest
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
