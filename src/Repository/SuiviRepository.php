<?php

namespace App\Repository;

use App\Entity\Suivi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Suivi>
 *
 * @method Suivi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suivi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suivi[]    findAll()
 * @method Suivi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suivi::class);
    }


    public function searchsuiviavecphoto($value): array
        {
            return $this->createQueryBuilder('s')
                ->andWhere('cd IS NOT NULL')
                ->leftJoin('s.photosarchives', 'cd')
                ->orderBy('s.id', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }
    public function searchCumulPoids($client, $centre, $startDate, $endDate, $evenement, $matiere)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.flux')

            ->addSelect( 'SUM(CASE WHEN s.typecontenant = :benne THEN s.poids ELSE 0 END)  as poids_total')
            ->addSelect('SUM(CASE WHEN s.typecontenant = :benne THEN 0  ELSE s.poids END)  as poids_totalbac')
            ->addSelect('SUM(CASE WHEN s.typecontenant = :benne THEN 0 ELSE s.collecte END) as bacsortis')
            ->addSelect('SUM(CASE WHEN s.typecontenant = :benne THEN s.collecte ELSE 0 END) as bennessorties')
            ->addSelect('SUM(CASE WHEN s.poids > 0 AND s.typecontenant = :benne THEN s.collecte ELSE 0 END) as contenantsorti_conditionnel')
            ->setParameter('benne', 'Benne')
            ->groupBy('s.flux');

        if ($client) {
            $qb->andWhere('s.leclient = :clientId')
                ->setParameter('clientId', $client);
        }
        if (!empty($evenement)) {
            $qb->andWhere('s.evenement = :evenement')
                ->setParameter('evenement', $evenement);
        }
        if (!empty($matiere)) {
            $qb->andWhere('s.flux = :matiere')
                ->setParameter('matiere', $matiere);
        }

        if ($centre) {
            $qb->andWhere('s.centredetris = :centrdetri')
                ->setParameter('centrdetri', $centre);
        }

        if ($startDate && $endDate) {
            $qb->andWhere('s.datedesoumission BETWEEN :start_date AND :end_date')
                ->setParameter('start_date', $startDate)
                ->setParameter('end_date', $endDate);
        }

        if ($startDate && !$endDate) {
            $qb->andWhere('s.datedesoumission >= :start_date ')
                ->setParameter('start_date', $startDate);
        }

        if (!$startDate && $endDate) {
            $qb->andWhere('s.datedesoumission <= :end_date ')
                ->setParameter('end_date', $endDate);
        }

        // Exécuter la requête et obtenir les résultats
        $results = $qb->getQuery()->getResult();
        $poidstotal = [];
        $dates = [];
        // Convertir le tableau de résultats en un format approprié
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = [
                'matiere' => $result['flux'],
                'poids_total' => (float) $result['poids_total'], // Assurez-vous que le poids est converti en nombre flottant
                'bacsortis' => (float) $result['bacsortis'],
                'bennessorties' => (float) $result['bennessorties'],
                'bennepese' => (float) $result['contenantsorti_conditionnel'],
                'poids_totalbac'=> (float) $result['poids_totalbac']
            ];
            $poidstotal = $result['poids_total'];
        }

        return [
            'formatedResult' => $formattedResults,
            'poidsTotal' => $poidstotal,
        ];
    }
    public function getMinDate()
    {
        $qb = $this->createQueryBuilder('s')
            ->select('MIN(s.datedesoumission) as date_min');

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result;
    }
    public function getMaxDate()
    {
        $qb = $this->createQueryBuilder('s')
            ->select('MAX(s.datedesoumission) as date_max');

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result;
    }

    public function getNbContenantByCentreDetri(): array
    {
        return $this->createQueryBuilder('s')
            ->select('cd.nom AS centre_nom, s.flux, SUM(s.poids) as nb_de_contenants')
            ->leftJoin('s.centredetris', 'cd')
            ->where('s.typecontenant = :benne')
            ->setParameter('benne', 'Benne')
            ->groupBy('cd.nom, s.flux')
            ->getQuery()
            ->getResult();
    }


    public function getSuivisGroupedByDateAndMatiere($client, $centre, $startDate, $endDate, $evenement, $matiere)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s', 'SUM(s.poids) as poids_total')
            ->where('s.typecontenant = :benne')
            ->setParameter('benne', 'Benne')
            ->groupBy('s.datedesoumission', 's.flux', 's.id'); // Group by 's.id' to include all suivis

        if (!empty($client)) {
            $qb->andWhere('s.leclient = :client')
                ->setParameter('client', $client);
        }
        if (!empty($evenement)) {
            $qb->andWhere('s.evenement = :evenement')
                ->setParameter('evenement', $evenement);
        }
        if (!empty($matiere)) {
            $qb->andWhere('s.flux = :matiere')
                ->setParameter('matiere', $matiere);
        }

        if (!empty($centre)) {
            $qb->andWhere('s.centredetris = :hall')
                ->setParameter('hall', $centre);
        }

        if ($startDate && $endDate) {
            $qb->andWhere('s.datedesoumission BETWEEN :start_date AND :end_date')
                ->setParameter('start_date', $startDate)
                ->setParameter('end_date', $endDate);
        }

        if ($startDate && !$endDate) {
            $qb->andWhere('s.datedesoumission >= :start_date ')
                ->setParameter('start_date', $startDate);
        }
        if (!$startDate && $endDate) {
            $qb->andWhere('s.datedesoumission <= :end_date ')
                ->setParameter('end_date', $endDate);
        }

        $results = $qb->getQuery()->getResult();

        $groupedSuivis = [];
        $poidstotal = 0;

        foreach ($results as $result) {
            /** @var Suivi $suivi */
            $suivi = $result[0]; // La première colonne contient l'entité Suivi complète
            $date = $suivi->getDatedesoumission()->format('Y-m-d');
            $matiere = $suivi->getFlux();
            $poids = (float) $result['poids_total'];

            // Initialisation du tableau pour la date si ce n'est pas déjà fait
            if (!isset($groupedSuivis[$date])) {
                $groupedSuivis[$date] = [];
            }

            // Initialisation du tableau pour la matière à la date actuelle si ce n'est pas déjà fait
            if (!isset($groupedSuivis[$date][$matiere])) {
                $groupedSuivis[$date][$matiere] = [];
            }

            // Ajout du suivi au tableau pour la matière à la date actuelle
            $groupedSuivis[$date][$matiere][] = [
                'suivi' => $suivi, // Ajouter l'entité Suivi complète
                'poids_total' => $poids,
            ];
            $poidstotal += $poids;
        }
        $dates = array_keys($groupedSuivis);
        $minDate = min($dates);
        $maxDate = max($dates);
        return [
            'groupedSuivis' => $groupedSuivis,
            'poidsTotal' => $poidstotal,
            'min'=>$minDate,
            'max'=>$maxDate,
        ];
    }





    //    /**
    //     * @return Suivi[] Returns an array of Suivi objects
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

    //    public function findOneBySomeField($value): ?Suivi
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
