<?php

namespace App\Repository;

use App\Entity\CartDishes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartDishes|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartDishes|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartDishes[]    findAll()
 * @method CartDishes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartDishesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartDishes::class);
    }

    // /**
    //  * @return CartDishes[] Returns an array of CartDishes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CartDishes
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
