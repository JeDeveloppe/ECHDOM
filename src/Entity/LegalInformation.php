<?php

namespace App\Entity;

use App\Repository\LegalInformationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LegalInformationRepository::class)]
class LegalInformation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $streetCompany = null;

    #[ORM\Column]
    private ?int $postalCodeCompany = null;

    #[ORM\Column(length: 255)]
    private ?string $cityCompany = null;

    #[ORM\Column(length: 255)]
    private ?string $siretCompany = null;

    #[ORM\Column(length: 255)]
    private ?string $emailCompany = null;

    #[ORM\Column(length: 255)]
    private ?string $webmasterCompanyName = null;

    #[ORM\Column(length: 255)]
    private ?string $webmasterFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $webmasterLastName = null;

    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255)]
    private ?string $fullUrlCompany = null;

    #[ORM\Column(length: 255)]
    private ?string $hostName = null;

    #[ORM\Column(length: 255)]
    private ?string $hostStreet = null;

    #[ORM\Column]
    private ?int $hostPostalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $hostCity = null;

    #[ORM\Column(length: 20)]
    private ?string $hostPhone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $publicationManagerFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $publicationManagerLastName = null;

    #[ORM\Column(length: 18)]
    private ?string $phoneCompany = null;

    #[ORM\Column(length: 255)]
    private ?string $webdesignerName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreetCompany(): ?string
    {
        return $this->streetCompany;
    }

    public function setStreetCompany(string $streetCompany): static
    {
        $this->streetCompany = $streetCompany;

        return $this;
    }

    public function getPostalCodeCompany(): ?int
    {
        return $this->postalCodeCompany;
    }

    public function setPostalCodeCompany(int $postalCodeCompany): static
    {
        $this->postalCodeCompany = $postalCodeCompany;

        return $this;
    }

    public function getCityCompany(): ?string
    {
        return $this->cityCompany;
    }

    public function setCityCompany(string $cityCompany): static
    {
        $this->cityCompany = $cityCompany;

        return $this;
    }

    public function getSiretCompany(): ?string
    {
        return $this->siretCompany;
    }

    public function setSiretCompany(string $siretCompany): static
    {
        $this->siretCompany = $siretCompany;

        return $this;
    }

    public function getEmailCompany(): ?string
    {
        return $this->emailCompany;
    }

    public function setEmailCompany(string $emailCompany): static
    {
        $this->emailCompany = $emailCompany;

        return $this;
    }

    public function getWebmasterCompanyName(): ?string
    {
        return $this->webmasterCompanyName;
    }

    public function setWebmasterCompanyName(string $webmasterCompanyName): static
    {
        $this->webmasterCompanyName = $webmasterCompanyName;

        return $this;
    }

    public function getWebmasterFirstName(): ?string
    {
        return $this->webmasterFirstName;
    }

    public function setWebmasterFirstName(string $webmasterFirstName): static
    {
        $this->webmasterFirstName = $webmasterFirstName;

        return $this;
    }

    public function getWebmasterLastName(): ?string
    {
        return $this->webmasterLastName;
    }

    public function setWebmasterLastName(string $webmasterLastName): static
    {
        $this->webmasterLastName = $webmasterLastName;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getFullUrlCompany(): ?string
    {
        return $this->fullUrlCompany;
    }

    public function setFullUrlCompany(string $fullUrlCompany): static
    {
        $this->fullUrlCompany = $fullUrlCompany;

        return $this;
    }

    public function getHostName(): ?string
    {
        return $this->hostName;
    }

    public function setHostName(string $hostName): static
    {
        $this->hostName = $hostName;

        return $this;
    }

    public function getHostStreet(): ?string
    {
        return $this->hostStreet;
    }

    public function setHostStreet(string $hostStreet): static
    {
        $this->hostStreet = $hostStreet;

        return $this;
    }

    public function getHostPostalCode(): ?int
    {
        return $this->hostPostalCode;
    }

    public function setHostPostalCode(int $hostPostalCode): static
    {
        $this->hostPostalCode = $hostPostalCode;

        return $this;
    }

    public function getHostCity(): ?string
    {
        return $this->hostCity;
    }

    public function setHostCity(string $hostCity): static
    {
        $this->hostCity = $hostCity;

        return $this;
    }

    public function getHostPhone(): ?string
    {
        return $this->hostPhone;
    }

    public function setHostPhone(string $hostPhone): static
    {
        $this->hostPhone = $hostPhone;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPublicationManagerFirstName(): ?string
    {
        return $this->publicationManagerFirstName;
    }

    public function setPublicationManagerFirstName(string $publicationManagerFirstName): static
    {
        $this->publicationManagerFirstName = $publicationManagerFirstName;

        return $this;
    }

    public function getPublicationManagerLastName(): ?string
    {
        return $this->publicationManagerLastName;
    }

    public function setPublicationManagerLastName(string $publicationManagerLastName): static
    {
        $this->publicationManagerLastName = $publicationManagerLastName;

        return $this;
    }

    public function getPhoneCompany(): ?string
    {
        return $this->phoneCompany;
    }

    public function setPhoneCompany(string $phoneCompany): static
    {
        $this->phoneCompany = $phoneCompany;

        return $this;
    }

    public function getWebdesignerName(): ?string
    {
        return $this->webdesignerName;
    }

    public function setWebdesignerName(string $webdesignerName): static
    {
        $this->webdesignerName = $webdesignerName;

        return $this;
    }
}
