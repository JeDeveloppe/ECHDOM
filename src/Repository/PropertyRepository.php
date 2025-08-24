<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\Workplace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Home>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * Trouve les entités Home à moins de $distanceKm d'un point donné.
     *
     * @param Workplace $workplace
     * @param int $distanceKm Distance en kilomètres.
     * @return Property[]
     */
    public function findPropertiesNearWorkplace(Workplace $workplace, int $distanceKm = 20): array
    {
        
        $longitude = $workplace->getLongitude();
        $latitude = $workplace->getLatitude();
        $excludedHomeId = $workplace->getOwner()->getProperties()->first()->getId() ?? null;

        // La requête SQL native est la même, mais elle cible la table 'home'
        $sql = "
            SELECT p.*
            FROM property p
            WHERE (6371 * acos(
                cos(radians(:lat))
                * cos(radians(p.latitude))
                * cos(radians(p.longitude) - radians(:lng))
                + sin(radians(:lat))
                * sin(radians(p.latitude))
            )) < :distance
        ";

        // Ajout de la condition pour exclure le logement de l'utilisateur
        if ($excludedHomeId !== null) {
            $sql .= " AND p.id != :excludedHomeId";
        }

        // Le ResultSetMapping doit être mis à jour pour mapper les résultats à l'entité Home
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult('App\Entity\Property', 'p');
        $rsm->addFieldResult('p', 'id', 'id');
        $rsm->addFieldResult('p', 'address', 'address');
        $rsm->addFieldResult('p', 'latitude', 'latitude');
        $rsm->addFieldResult('p', 'longitude', 'longitude');
        $rsm->addFieldResult('p', 'user_id', 'user');

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
