<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HomeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HomeRepository::class)]
class Home implements GeolocatableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 400)]
    private ?string $address = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6)]
    private ?string $longitude = null;

    #[Groups(['home:details'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'homes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['home:details'])]
    private ?HomeType $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Groups(['home:details'])]
    private ?string $surface = null;

    #[ORM\Column]
    #[Groups(['home:details'])]
    private ?int $rooms = null;

    #[ORM\Column]
    #[Groups(['home:details'])]
    private ?int $bedrooms = null;

    #[ORM\Column]
    #[Groups(['home:details'])]
    private ?int $bathrooms = null;

    #[ORM\ManyToOne(inversedBy: 'homes')]
    #[Groups(['home:details'])]
    private ?FloorLevel $floor = null;

    #[ORM\Column]
    #[Groups(['home:details'])]
    private ?bool $hasElevator = null;

    #[ORM\Column]
    #[Groups(['home:details'])]
    private ?bool $hasBalcony = null;

    #[Groups(['home:details'])]
    private ?int $timeTravelBetweenHomeAndWorkplace = null; // Ajout d'une propriété pour le temps de trajet

    /**
     * @var Collection<int, Photo>
     */
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'home')]
    private Collection $photos;

    /**
     * @var Collection<int, HomeEquipment>
     */
    #[ORM\ManyToMany(targetEntity: HomeEquipment::class, inversedBy: 'homes')]
    #[Groups(['home:details'])]
    private Collection $equipments;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['home:details'])]
    private ?string $otherRules = null;

    /**
     * @var Collection<int, HomeRegulationsAndRestrictions>
     */
    #[ORM\ManyToMany(targetEntity: HomeRegulationsAndRestrictions::class, inversedBy: 'homes')]
    private Collection $rules;

    #[ORM\ManyToOne(inversedBy: 'homes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column]
    private ?bool $hasGarage = null;

    #[ORM\Column]
    private ?bool $hasParking = null;

    #[ORM\ManyToOne(inversedBy: 'homesWithGarage')]
    private ?HomeTypeOfParkingAndGarage $TypeOfGarage = null;

    #[ORM\ManyToOne(inversedBy: 'homesWithParking')]
    private ?HomeTypeOfParkingAndGarage $TypeOfParking = null;

    /**
     * @var Collection<int, HomeAvailability>
     */
    #[ORM\OneToMany(targetEntity: HomeAvailability::class, mappedBy: 'home', orphanRemoval: true)]
    private Collection $homeAvailabilities;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->equipments = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->homeAvailabilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?HomeType
    {
        return $this->type;
    }

    public function setType(?HomeType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSurface(): ?string
    {
        return $this->surface;
    }

    public function setSurface(string $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): static
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getBathrooms(): ?int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(int $bathrooms): static
    {
        $this->bathrooms = $bathrooms;

        return $this;
    }

    public function getFloor(): ?FloorLevel
    {
        return $this->floor;
    }

    public function setFloor(?FloorLevel $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    public function hasElevator(): ?bool
    {
        return $this->hasElevator;
    }

    public function setHasElevator(bool $hasElevator): static
    {
        $this->hasElevator = $hasElevator;

        return $this;
    }

    public function hasBalcony(): ?bool
    {
        return $this->hasBalcony;
    }

    public function setHasBalcony(bool $hasBalcony): static
    {
        $this->hasBalcony = $hasBalcony;

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setHome($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getHome() === $this) {
                $photo->setHome(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HomeEquipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(HomeEquipment $equipment): static
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(HomeEquipment $equipment): static
    {
        $this->equipments->removeElement($equipment);

        return $this;
    }

    public function getOtherRules(): ?string
    {
        return $this->otherRules;
    }

    public function setOtherRules(?string $otherRules): static
    {
        $this->otherRules = $otherRules;

        return $this;
    }

    /**
     * @return Collection<int, HomeRegulationsAndRestrictions>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(HomeRegulationsAndRestrictions $rule): static
    {
        if (!$this->rules->contains($rule)) {
            $this->rules->add($rule);
        }

        return $this;
    }

    public function removeRule(HomeRegulationsAndRestrictions $rule): static
    {
        $this->rules->removeElement($rule);

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTimeTravelBetweenHomeAndWorkplace(): ?int
    {
        return $this->timeTravelBetweenHomeAndWorkplace;
    }
    public function setTimeTravelBetweenHomeAndWorkplace(?int $timeTravel): static
    {
        $this->timeTravelBetweenHomeAndWorkplace = $timeTravel;

        return $this;
    }

    public function hasGarage(): ?bool
    {
        return $this->hasGarage;
    }

    public function setHasGarage(bool $hasGarage): static
    {
        $this->hasGarage = $hasGarage;

        return $this;
    }

    public function hasParking(): ?bool
    {
        return $this->hasParking;
    }

    public function setHasParking(bool $hasParking): static
    {
        $this->hasParking = $hasParking;

        return $this;
    }

    public function getTypeOfGarage(): ?HomeTypeOfParkingAndGarage
    {
        return $this->TypeOfGarage;
    }

    public function setTypeOfGarage(?HomeTypeOfParkingAndGarage $TypeOfGarage): static
    {
        $this->TypeOfGarage = $TypeOfGarage;

        return $this;
    }

    public function getTypeOfParking(): ?HomeTypeOfParkingAndGarage
    {
        return $this->TypeOfParking;
    }

    public function setTypeOfParking(?HomeTypeOfParkingAndGarage $TypeOfParking): static
    {
        $this->TypeOfParking = $TypeOfParking;

        return $this;
    }

    /**
     * @return Collection<int, HomeAvailability>
     */
    public function getHomeAvailabilities(): Collection
    {
        return $this->homeAvailabilities;
    }

    public function addHomeAvailability(HomeAvailability $homeAvailability): static
    {
        if (!$this->homeAvailabilities->contains($homeAvailability)) {
            $this->homeAvailabilities->add($homeAvailability);
            $homeAvailability->setHome($this);
        }

        return $this;
    }

    public function removeHomeAvailability(HomeAvailability $homeAvailability): static
    {
        if ($this->homeAvailabilities->removeElement($homeAvailability)) {
            // set the owning side to null (unless already changed)
            if ($homeAvailability->getHome() === $this) {
                $homeAvailability->setHome(null);
            }
        }

        return $this;
    }

}
