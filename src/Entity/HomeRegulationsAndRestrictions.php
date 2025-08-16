<?php

namespace App\Entity;

use App\Repository\HomeRegulationsAndRestrictionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomeRegulationsAndRestrictionsRepository::class)]
class HomeRegulationsAndRestrictions
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
    #[ORM\ManyToMany(targetEntity: Home::class, mappedBy: 'rules')]
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
            $home->addRule($this);
        }

        return $this;
    }

    public function removeHome(Home $home): static
    {
        if ($this->homes->removeElement($home)) {
            $home->removeRule($this);
        }

        return $this;
    }
}
