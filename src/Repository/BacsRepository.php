<?php

namespace App\Repository;

use App\Entity\Bacs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bacs>
 *
 * @method Bacs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bacs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bacs[]    findAll()
 * @method Bacs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bacs::class);
    }


    //    /**
    //     * @return Bacs[] Returns an array of Bacs objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Bacs
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
