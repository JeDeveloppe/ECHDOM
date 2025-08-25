<?php

namespace App\Service;

use App\Entity\LegalInformation;
use App\Repository\LegalInformationRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LegalInformationService
{
    public function __construct(
        private LegalInformationRepository $legalInformationRepository,
        private EntityManagerInterface $manager,
        ){
    }

    public function creationLegalInformation(): void
    {

        
        //on vérifié si pn a déjà créer l'administrateur spécial
        $legal = $this->legalInformationRepository->findOneBy(['companyName' => 'JE DÉVELOPPE']);

        if(!$legal){
            $legal = new LegalInformation();
        }

            $legal
                ->setCompanyName('WORKAway')
                ->setStreetCompany('64b rue Émeraude')
                ->setPostalCodeCompany(14540)
                ->setCityCompany('BOURGUEBUS')
                ->setPublicationManagerFirstName('René')
                ->setPublicationManagerLastName('WETTA')
                ->setSiretCompany('899 753 439 00019')
                ->setPhoneCompany('06.42.02.56.29')
                ->setEmailCompany('contact@workaway.fr')
                ->setFullUrlCompany('http://www.workaway.fr')
                ->setHostName('IONOS SARL')
                ->setHostStreet('7 place de la gare')
                ->setHostPostalCode(57200)
                ->setHostCity('SARREGUEMINES')
                ->setHostPhone('09.70.80.89.11')
                ->setWebmasterCompanyName('Je-Développe')
                ->setWebmasterFirstName('René')
                ->setWebmasterLastName('WETTA')
                ->setWebdesignerName('(aucun)')
                ->setUpdatedAt(new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
    
            $this->manager->persist($legal);
            $this->manager->flush();

    }

}