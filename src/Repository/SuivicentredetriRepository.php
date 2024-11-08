<?php

namespace App\Repository;

use App\Entity\Suivicentredetri;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Suivicentredetri>
 *
 * @method Suivicentredetri|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suivicentredetri|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suivicentredetri[]    findAll()
 * @method Suivicentredetri[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuivicentredetriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suivicentredetri::class);
    }

    //    /**
    //     * @return Suivicentredetri[] Returns an array of Suivicentredetri objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Suivicentredetri
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
