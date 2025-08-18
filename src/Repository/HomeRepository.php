<?php

namespace App\Repository;

use App\Entity\Home;
use App\Entity\Workplace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Home>
 */
class HomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Home::class);
    }

    /**
     * Trouve les entités Home à moins de $distanceKm d'un point donné.
     *
     * @param Workplace $workplace
     * @param int $distanceKm Distance en kilomètres.
     * @return Home[]
     */
    public function findHomesNearWorkplace(Workplace $workplace, int $distanceKm = 20): array
    {
        
        $longitude = $workplace->getLongitude();
        $latitude = $workplace->getLatitude();
        $excludedHomeId = $workplace->getOwner()->getHomes()->first()->getId() ?? null;

        // La requête SQL native est la même, mais elle cible la table 'home'
        $sql = "
            SELECT h.*
            FROM home h
            WHERE (6371 * acos(
                cos(radians(:lat))
                * cos(radians(h.latitude))
                * cos(radians(h.longitude) - radians(:lng))
                + sin(radians(:lat))
                * sin(radians(h.latitude))
            )) < :distance
        ";

        // Ajout de la condition pour exclure le logement de l'utilisateur
        if ($excludedHomeId !== null) {
            $sql .= " AND h.id != :excludedHomeId";
        }

        // Le ResultSetMapping doit être mis à jour pour mapper les résultats à l'entité Home
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult('App\Entity\Home', 'h');
        $rsm->addFieldResult('h', 'id', 'id');
        $rsm->addFieldResult('h', 'address', 'address');
        $rsm->addFieldResult('h', 'latitude', 'latitude');
        $rsm->addFieldResult('h', 'longitude', 'longitude');
        $rsm->addFieldResult('h', 'user_id', 'user');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        $query->setParameter('lat', $latitude);
        $query->setParameter('lng', $longitude);
        $query->setParameter('distance', $distanceKm);

        // On lie le paramètre d'exclusion si il est présent
        if ($excludedHomeId !== null) {
            $query->setParameter('excludedHomeId', $excludedHomeId);
        }

        return $query->getResult();
    }

    //    /**
    //     * @return Home[] Returns an array of Home objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Home
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
