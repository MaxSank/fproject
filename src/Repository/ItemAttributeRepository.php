<?php

namespace App\Repository;

use App\Entity\ItemAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemAttribute[]    findAll()
 * @method ItemAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemAttribute::class);
    }

    // /**
    //  * @return ItemAttribute[] Returns an array of ItemAttribute objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ItemAttribute
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
