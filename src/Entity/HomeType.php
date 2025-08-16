<?php

namespace App\Entity;

use App\Repository\HomeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomeTypeRepository::class)]
class HomeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\OneToMany(targetEntity: Home::class, mappedBy: 'type')]
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
            $home->setType($this);
        }

        return $this;
    }

    public function removeHome(Home $home): static
    {
        if ($this->homes->removeElement($home)) {
            // set the owning side to null (unless already changed)
            if ($home->getType() === $this) {
                $home->setType(null);
            }
        }

        return $this;
    }
}
