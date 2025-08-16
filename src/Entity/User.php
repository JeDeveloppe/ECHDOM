<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $biography = null;

    #[ORM\Column]
    private ?bool $verified = null;

    #[ORM\Column]
    private ?int $pointsGained = null;

    #[ORM\Column]
    private ?int $pointsSpent = null;

    /**
     * @var Collection<int, Workplace>
     */
    #[ORM\OneToMany(targetEntity: Workplace::class, mappedBy: 'owner')]
    private Collection $workplaces;

    /**
     * @var Collection<int, Home>
     */
    #[ORM\OneToMany(targetEntity: Home::class, mappedBy: 'owner')]
    private Collection $homes;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'proposer')]
    private Collection $exchangesProposed;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'receiver')]
    private Collection $exchangesReceiver;

    #[ORM\OneToOne(mappedBy: 'rater', cascade: ['persist', 'remove'])]
    private ?Notation $notationRater = null;

    #[ORM\OneToOne(mappedBy: 'ratedUser', cascade: ['persist', 'remove'])]
    private ?Notation $notationRated = null;

    public function __construct()
    {
        $this->workplaces = new ArrayCollection();
        $this->homes = new ArrayCollection();
        $this->exchangesProposed = new ArrayCollection();
        $this->exchangesReceiver = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): static
    {
        $this->biography = $biography;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): static
    {
        $this->verified = $verified;

        return $this;
    }

    public function getPointsGained(): ?int
    {
        return $this->pointsGained;
    }

    public function setPointsGained(int $pointsGained): static
    {
        $this->pointsGained = $pointsGained;

        return $this;
    }

    public function getPointsSpent(): ?int
    {
        return $this->pointsSpent;
    }

    public function setPointsSpent(int $pointsSpent): static
    {
        $this->pointsSpent = $pointsSpent;

        return $this;
    }

    /**
     * @return Collection<int, Workplace>
     */
    public function getWorkplaces(): Collection
    {
        return $this->workplaces;
    }

    public function addWorkplace(Workplace $workplace): static
    {
        if (!$this->workplaces->contains($workplace)) {
            $this->workplaces->add($workplace);
            $workplace->setOwner($this);
        }

        return $this;
    }

    public function removeWorkplace(Workplace $workplace): static
    {
        if ($this->workplaces->removeElement($workplace)) {
            // set the owning side to null (unless already changed)
            if ($workplace->getOwner() === $this) {
                $workplace->setOwner(null);
            }
        }

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
            $home->setOwner($this);
        }

        return $this;
    }

    public function removeHome(Home $home): static
    {
        if ($this->homes->removeElement($home)) {
            // set the owning side to null (unless already changed)
            if ($home->getOwner() === $this) {
                $home->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exchange>
     */
    public function getExchangesProposed(): Collection
    {
        return $this->exchangesProposed;
    }

    public function addExchangesProposed(Exchange $exchangesProposed): static
    {
        if (!$this->exchangesProposed->contains($exchangesProposed)) {
            $this->exchangesProposed->add($exchangesProposed);
            $exchangesProposed->setProposer($this);
        }

        return $this;
    }

    public function removeExchangesProposed(Exchange $exchangesProposed): static
    {
        if ($this->exchangesProposed->removeElement($exchangesProposed)) {
            // set the owning side to null (unless already changed)
            if ($exchangesProposed->getProposer() === $this) {
                $exchangesProposed->setProposer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exchange>
     */
    public function getExchangesReceiver(): Collection
    {
        return $this->exchangesReceiver;
    }

    public function addExchangesReceiver(Exchange $exchangesReceiver): static
    {
        if (!$this->exchangesReceiver->contains($exchangesReceiver)) {
            $this->exchangesReceiver->add($exchangesReceiver);
            $exchangesReceiver->setReceiver($this);
        }

        return $this;
    }

    public function removeExchangesReceiver(Exchange $exchangesReceiver): static
    {
        if ($this->exchangesReceiver->removeElement($exchangesReceiver)) {
            // set the owning side to null (unless already changed)
            if ($exchangesReceiver->getReceiver() === $this) {
                $exchangesReceiver->setReceiver(null);
            }
        }

        return $this;
    }

    public function getNotationRater(): ?Notation
    {
        return $this->notationRater;
    }

    public function setNotationRater(Notation $notationRater): static
    {
        // set the owning side of the relation if necessary
        if ($notationRater->getRater() !== $this) {
            $notationRater->setRater($this);
        }

        $this->notationRater = $notationRater;

        return $this;
    }

    public function getNotationRated(): ?Notation
    {
        return $this->notationRated;
    }

    public function setNotationRated(Notation $notationRated): static
    {
        // set the owning side of the relation if necessary
        if ($notationRated->getRatedUser() !== $this) {
            $notationRated->setRatedUser($this);
        }

        $this->notationRated = $notationRated;

        return $this;
    }
}
