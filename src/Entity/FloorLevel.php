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
    #[Groups(['home:details'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\OneToMany(targetEntity: Home::class, mappedBy: 'floor')]
    private Collection $homes;

    public function __construct()
    {
        $this->homes = new ArrayCollection();
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
    public function getHomes(): Collection
    {
        return $this->homes;
    }

    public function addHome(Home $home): static
    {
        if (!$this->homes->contains($home)) {
            $this->homes->add($home);
            $home->setFloor($this);
        }

        return $this;
    }

    public function removeHome(Home $home): static
    {
        if ($this->homes->removeElement($home)) {
            // set the owning side to null (unless already changed)
            if ($home->getFloor() === $this) {
                $home->setFloor(null);
            }
        }

        return $this;
    }
}
