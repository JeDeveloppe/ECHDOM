<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PropertyEquipmentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PropertyEquipmentRepository::class)]
class PropertyEquipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['property:details'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\ManyToMany(targetEntity: Property::class, mappedBy: 'equipments')]
    private Collection $properties;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Home>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(Property $property): static
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
            $property->addEquipment($this);
        }

        return $this;
    }

    public function removeHome(Property $property): static
    {
        if ($this->properties->removeElement($property)) {
            $property->removeEquipment($this);
        }

        return $this;
    }

}
