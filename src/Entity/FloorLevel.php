<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FloorLevelRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FloorLevelRepository::class)]
class FloorLevel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['property:details'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\OneToMany(targetEntity: Property::class, mappedBy: 'floor')]
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
            $property->setFloor($this);
        }

        return $this;
    }

    public function removeHome(Property $property): static
    {
        if ($this->properties->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getFloor() === $this) {
                $property->setFloor(null);
            }
        }

        return $this;
    }
}
