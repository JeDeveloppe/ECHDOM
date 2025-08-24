<?php

namespace App\Service;

use App\Entity\Property;
use App\Entity\PropertyAvailability;
use App\Repository\PropertyAvailabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeZone;

class PropertyAvailabilityService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PropertyAvailabilityRepository $propertyAvailabilityRepository,
    ) {
    }

    public function handleAvailability(Property $property, DateTimeImmutable $startAt, DateTimeImmutable $endAt, array $weeklyDays): void
    {
        // 1. Récupérer toutes les disponibilités existantes pour la période en une seule requête
        $existingAvailabilities = $this->propertyAvailabilityRepository->findAvailabilitiesForHomeAndPeriod(
            $property,
            $startAt,
            $endAt
        );

        $existingAvailabilitiesMap = [];
        foreach ($existingAvailabilities as $availability) {
            $existingAvailabilitiesMap[$availability->getStartAt()->format('Y-m-d')] = $availability;
        }

        // 2. Boucler sur les dates de la période et gérer la création/mise à jour
        // Ajuster l'heure de début à 12:00:00
        $startAt = $startAt->modify('12:00:00');
        
        // La période se termine un jour après la date de fin, pour inclure la dernière journée
        $period = new DatePeriod($startAt, new DateInterval('P1D'), $endAt->modify('+1 day'));
        $timezone = new DateTimeZone('Europe/Paris');
        $now = new DateTimeImmutable('now', $timezone);

        foreach ($period as $date) {
            $immutableDate = $date;
            $dayOfWeek = (int) $immutableDate->format('N');

            if (in_array($dayOfWeek, $weeklyDays)) {
                $dateKey = $immutableDate->format('Y-m-d');
                
                // Définir les heures spécifiques de début et de fin
                $startOfDay = $immutableDate->modify('12:00:00');
                // La fin de la disponibilité est le jour suivant à 11:59:59
                $endOfDay = $immutableDate->modify('+1 day 11:59:59');

                // Vérifier si une disponibilité existe déjà pour cette date
                if (isset($existingAvailabilitiesMap[$dateKey])) {
                    // Mettre à jour la disponibilité existante
                    $existingAvailability = $existingAvailabilitiesMap[$dateKey];
                    $existingAvailability->setStartAt($startOfDay);
                    $existingAvailability->setEndAt($endOfDay);
                } else {
                    // Créer une nouvelle disponibilité
                    $newAvailability = new PropertyAvailability();
                    $newAvailability->setHome($property); 
                    $newAvailability->setCreatedAt($now);
                    $newAvailability->setStartAt($startOfDay);
                    $newAvailability->setEndAt($endOfDay);
                    $this->em->persist($newAvailability);
                }
            }
        }
        $this->em->flush();
    }
}
