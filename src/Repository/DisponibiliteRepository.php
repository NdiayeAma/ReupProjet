<?php

namespace App\Repository;

use App\Entity\Disponibilite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DisponibiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disponibilite::class);
    }

    public function findByUser($userId)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.agent_id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find disponibilites by date range.
     *
     * @param \DateTime $debut
     * @param \DateTime $fin
     * @return Disponibilite[]
     */
    public function findByDateRange(\DateTime $debut, \DateTime $fin): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.debut >= :debut AND d.fin <= :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getResult();
    }
}