<?php

namespace App\Repository;

use App\Entity\Donation;
use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Donation>
 */
class DonationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donation::class);
    }
    public function findByEvenementAndWoodType(Evenement $evenement, string $woodType)
    {
        $donations = $this->createQueryBuilder('d')
            ->andWhere('d.evenement = :evenement')
            ->setParameter('evenement', $evenement)
            ->getQuery()
            ->getResult();

        if($woodType!="Tous"){
            return array_filter($donations, function ($donation) use ($woodType) {
                return in_array($woodType, $donation->getWoodTypes() ?? []);
            });
        }else{
            return $donations;
        }


    }

    //    /**
    //     * @return Donation[] Returns an array of Donation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Donation
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
