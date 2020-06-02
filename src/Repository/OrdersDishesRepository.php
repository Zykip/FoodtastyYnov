<?php

namespace App\Repository;

use App\Entity\OrdersDishes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrdersDishes|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdersDishes|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdersDishes[]    findAll()
 * @method OrdersDishes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersDishesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdersDishes::class);
    }

    // /**
    //  * @return OrdersDishes[] Returns an array of OrdersDishes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrdersDishes
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
