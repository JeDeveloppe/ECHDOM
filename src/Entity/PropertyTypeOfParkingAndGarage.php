<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PropertyTypeOfParkingAndGarageRepository;

#[ORM\Entity(repositoryClass: PropertyTypeOfParkingAndGarageRepository::class)]
class PropertyTypeOfParkingAndGarage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['property:details'])]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isForParkingOnly = null;

    #[ORM\Column]
    private ?bool $isForGarageOnly = null;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\OneToMany(targetEntity: Property::class, mappedBy: 'TypeOfGarage')]
    private Collection $propertiesWithGarage;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\OneToMany(targetEntity: Property::class, mappedBy: 'TypeOfParking')]
    private Collection $propertiesWithParking;

    public function __construct()
    {
        $this->propertiesWithGarage = new ArrayCollection();
        $this->propertiesWithParking = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isForParkingOnly(): ?bool
    {
        return $this->isForParkingOnly;
    }

    public function setIsForParkingOnly(bool $isForParkingOnly): static
    {
        $this->isForParkingOnly = $isForParkingOnly;

        return $this;
    }

    public function isForGarageOnly(): ?bool
    {
        return $this->isForGarageOnly;
    }

    public function setIsForGarageOnly(bool $isForGarageOnly): static
    {
        $this->isForGarageOnly = $isForGarageOnly;

        return $this;
    }

    /**
     * @return Collection<int, Home>
     */
    public function getPropertiesWithGarage(): Collection
    {
        return $this->propertiesWithGarage;
    }

    public function addPropertyWithGarage(Property $property): static
    {
        if (!$this->propertiesWithGarage->contains($property)) {
            $this->propertiesWithGarage->add($property);
            $property->setTypeOfGarage($this);
        }

        return $this;
    }

    public function removeHomeWithGarage(Property $property): static
    {
        if ($this->propertiesWithGarage->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getTypeOfGarage() === $this) {
                $property->setTypeOfGarage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Home>
     */
    public function getPropertiesWithParking(): Collection
    {
        return $this->propertiesWithParking;
    }
    public function addPropertyWithParking(Property $property): static
    {
        if (!$this->propertiesWithParking->contains($property)) {
            $this->propertiesWithParking->add($property);
            $property->setTypeOfParking($this);
        }

        return $this;
    }    
    public function removeHomeWithParking(Property $property): static
    {
        if ($this->propertiesWithParking->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getTypeOfParking() === $this) {
                $property->setTypeOfParking(null);
            }
        }

        return $this;
    }
}
