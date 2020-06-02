<?php

namespace App\Repository;

use App\Entity\RestaurantsUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RestaurantsUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantsUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantsUsers[]    findAll()
 * @method RestaurantsUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantsUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantsUsers::class);
    }

    // /**
    //  * @return RestaurantsUsers[] Returns an array of RestaurantsUsers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RestaurantsUsers
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
