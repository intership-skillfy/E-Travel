<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'excursion' => Excursion::class,
    'hiking' => Hiking::class,
    'omra' => Omra::class,
    'trip' => Trip::class,
])]


#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $detailed_description = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $images = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $end_date = null;



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    private ?Category $category = null;

    /**
     * @var Collection<int, PriceList>
     */
    #[ORM\OneToMany(targetEntity: PriceList::class, mappedBy: 'offre')]
    private Collection $tarifs;



    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\ManyToMany(targetEntity: Reservation::class, mappedBy: 'offres')]
    private Collection $reservations;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    private ?Pack $pack = null;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'offre')]
    private Collection $reviews;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Agency $agency = null;


    public function __construct()
    {
        $this->tarifs = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDetailedDescription(): ?string
    {
        return $this->detailed_description;
    }

    public function setDetailedDescription(string $detailed_description): static
    {
        $this->detailed_description = $detailed_description;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeImmutable $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeImmutable $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }


    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, PriceList>
     */
    public function getTarifs(): Collection
    {
        return $this->tarifs;
    }

    public function addTarif(PriceList $tarif): static
    {
        if (!$this->tarifs->contains($tarif)) {
            $this->tarifs->add($tarif);
            $tarif->setOffre($this);
        }

        return $this;
    }

    public function removeTarif(PriceList $tarif): static
    {
        if ($this->tarifs->removeElement($tarif)) {
            // set the owning side to null (unless already changed)
            if ($tarif->getOffre() === $this) {
                $tarif->setOffre(null);
            }
        }

        return $this;
    }
     /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->addOffre($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            $reservation->removeOffre($this);
        }

        return $this;
    }

    public function getPack(): ?Pack
    {
        return $this->pack;
    }

    public function setPack(?Pack $pack): static
    {
        $this->pack = $pack;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setOffre($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getOffre() === $this) {
                $review->setOffre(null);
            }
        }

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): static
    {
        $this->agency = $agency;

        return $this;
    }
}


