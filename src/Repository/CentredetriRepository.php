<?php

namespace App\Repository;

use App\Entity\Centredetri;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Centredetri>
 *
 * @method Centredetri|null find($id, $lockMode = null, $lockVersion = null)
 * @method Centredetri|null findOneBy(array $criteria, array $orderBy = null)
 * @method Centredetri[]    findAll()
 * @method Centredetri[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CentredetriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Centredetri::class);
    }
    public function findCentresAvecBacSuperieurAUn($bacColumn):  array
    {
        return $this->createQueryBuilder('c')
            ->where(sprintf('c.%s > :bac', $bacColumn))
        ->setParameter('bac', 1)
        ->getQuery()
        ->getResult();

    }

    public function getphotobydate($date): array
    {
        return $this->createQueryBuilder('s')
            ->select('cd.nom')
            ->leftJoin('s.photosarchives', 'cd')
            ->where('cd.dateupload = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Centredetri[] Returns an array of Centredetri objects
    //
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Centredetri
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
