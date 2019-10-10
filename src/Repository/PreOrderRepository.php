<?php

namespace App\Repository;

use App\Entity\PreOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PreOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreOrder[]    findAll()
 * @method PreOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreOrder::class);
    }

    // /**
    //  * @return PreOrder[] Returns an array of PreOrder objects
    //  */
    public function pastOneDay($date)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.createdAt < :date')
            ->andWhere('p.status = :status')
            ->setParameter('date', $date)
            ->setParameter('status', PreOrder::STATUS_PENDING)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?PreOrder
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
