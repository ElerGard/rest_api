<?php

namespace App\Repository;

use App\Entity\Postik;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Postik|null find($id, $lockMode = null, $lockVersion = null)
 * @method Postik|null findOneBy(array $criteria, array $orderBy = null)
 * @method Postik[]    findAll()
 * @method Postik[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostikRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Postik::class);
    }

    // /**
    //  * @return Postik[] Returns an array of Postik objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Postik
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
