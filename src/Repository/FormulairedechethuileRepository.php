<?php

namespace App\Repository;

use App\Entity\Formulairedechethuile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formulairedechethuile>
 *
 * @method Formulairedechethuile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formulairedechethuile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formulairedechethuile[]    findAll()
 * @method Formulairedechethuile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormulairedechethuileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formulairedechethuile::class);
    }

    //    /**
    //     * @return Formulairedechethuile[] Returns an array of Formulairedechethuile objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Formulairedechethuile
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
