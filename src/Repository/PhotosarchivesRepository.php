<?php

namespace App\Repository;

use App\Entity\Photosarchives;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photosarchives>
 *
 * @method Photosarchives|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photosarchives|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photosarchives[]    findAll()
 * @method Photosarchives[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotosarchivesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photosarchives::class);
    }

    public function findPhotosByDateAndCentre($date, $centreDetri)
    {
        return $this->createQueryBuilder('p')
            ->where('p.centredetri = :centreDetri')
            ->andWhere('p.dateupload = :date')
            ->setParameter('centreDetri', $centreDetri)
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
    public function findPhotosByCentre($centreDetri)
    {
        return $this->createQueryBuilder('p')
            ->where('p.centredetri = :centreDetri')
            ->setParameter('centreDetri', $centreDetri)
            ->getQuery()
            ->getResult();
    }
    public function findPhotosByCentreandtypecontenant($centreDetri,$typecontenant)
    {
        return $this->createQueryBuilder('p')
            ->where('p.centredetri = :centreDetri')
            ->andWhere('p.titre LIKE :typecontenant')
            ->setParameter('centreDetri', $centreDetri)
            ->setParameter('typecontenant', '%' . $typecontenant . '%')
            ->getQuery()
            ->getResult();
    }
    public function findPhotosByDateAndCentreAndTypecontenant($date, $centreDetri,$typecontenant)
    {
        return $this->createQueryBuilder('p')
            ->where('p.centredetri = :centreDetri')
            ->andWhere('p.dateupload = :date')
            ->andWhere('p.titre LIKE :typecontenant')
            ->setParameter('centreDetri', $centreDetri)
            ->setParameter('date', $date)
            ->setParameter('typecontenant', '%' . $typecontenant . '%')
            ->getQuery()
            ->getResult();
    }





    //    /**
    //     * @return Photosarchives[] Returns an array of Photosarchives objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Photosarchives
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
