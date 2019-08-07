<?php

namespace App\Repository;

use App\Entity\BetSelection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BetSelection|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetSelection|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetSelection[]    findAll()
 * @method BetSelection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetSelectionsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BetSelection::class);
    }

    // /**
    //  * @return BetSelections[] Returns an array of BetSelections objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BetSelections
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
