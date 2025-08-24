<?php

namespace App\Repository;

use DateTimeImmutable;
use App\Entity\HomeAvailability;
use App\Entity\Property;
use App\Entity\PropertyAvailability;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<HomeAvailability>
 */
class PropertyAvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertyAvailability::class);
    }

    /**
     * @return HomeAvailability[]
     */
    public function findAvailabilitiesForHomeAndPeriod(Property $property, DateTimeImmutable $startAt, DateTimeImmutable $endAt): array
    {
        // Use withTime() instead of setTime() for immutable DateTime objects.
        // This method is safer as it always returns a new DateTimeImmutable object,
        // and never 'false', which resolves the type-hinting warning.
        $startAtMidnight = $startAt->setTime(0, 0, 0);
        $endAtEndOfDay = $endAt->setTime(23, 59, 59);

    
        return $this->createQueryBuilder('pa')
            ->where('pa.property = :property')
            ->andWhere('pa.startAt >= :startAt')
            ->andWhere('pa.endAt <= :endAt')
            ->setParameter('startAt', $startAtMidnight)
            ->setParameter('endAt', $endAtEndOfDay)
            ->setParameter('property', $property)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return HomeAvailability[] Returns an array of HomeAvailability objects
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

    //    public function findOneBySomeField($value): ?HomeAvailability
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
