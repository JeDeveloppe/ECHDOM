<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\HomeTypeOfParkingAndGarageRepository;

#[ORM\Entity(repositoryClass: HomeTypeOfParkingAndGarageRepository::class)]
class HomeTypeOfParkingAndGarage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['home:details'])]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isForParkingOnly = null;

    #[ORM\Column]
    private ?bool $isForGarageOnly = null;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\OneToMany(targetEntity: Home::class, mappedBy: 'TypeOfGarage')]
    private Collection $homesWithGarage;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\OneToMany(targetEntity: Home::class, mappedBy: 'TypeOfParking')]
    private Collection $homesWithParking;

    public function __construct()
    {
        $this->homesWithGarage = new ArrayCollection();
        $this->homesWithParking = new ArrayCollection();
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
    public function getHomesWithGarage(): Collection
    {
        return $this->homesWithGarage;
    }

    public function addHomeWithGarage(Home $home): static
    {
        if (!$this->homesWithGarage->contains($home)) {
            $this->homesWithGarage->add($home);
            $home->setTypeOfGarage($this);
        }

        return $this;
    }

    public function removeHomeWithGarage(Home $home): static
    {
        if ($this->homesWithGarage->removeElement($home)) {
            // set the owning side to null (unless already changed)
            if ($home->getTypeOfGarage() === $this) {
                $home->setTypeOfGarage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Home>
     */
    public function getHomesWithParking(): Collection
    {
        return $this->homesWithParking;
    }
    public function addHomeWithParking(Home $home): static
    {
        if (!$this->homesWithParking->contains($home)) {
            $this->homesWithParking->add($home);
            $home->setTypeOfParking($this);
        }

        return $this;
    }    
    public function removeHomeWithParking(Home $home): static
    {
        if ($this->homesWithParking->removeElement($home)) {
            // set the owning side to null (unless already changed)
            if ($home->getTypeOfParking() === $this) {
                $home->setTypeOfParking(null);
            }
        }

        return $this;
    }
}
