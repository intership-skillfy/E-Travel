<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
    #[Groups(["offre:read", "offre:write"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["offre:read", "offre:write"])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["offre:read", "offre:write"])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["offre:read", "offre:write"])]
    private ?string $detailed_description = null;

    #[ORM\Column(type: Types::ARRAY)]
    #[Groups(["offre:read", "offre:write"])]
    private array $images = [];

    #[ORM\Column]
    #[Groups(["offre:read", "offre:write"])]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column]
    #[Groups(["offre:read", "offre:write"])]
    private ?\DateTimeImmutable $end_date = null;

    #[ORM\OneToMany(targetEntity: PriceList::class, mappedBy: 'offre', cascade: ['persist', 'remove'])]
    #[Groups(["offre:read", "offre:write"])]
    private Collection $tarifs;

    #[ORM\ManyToMany(targetEntity: Reservation::class, inversedBy: 'offres', cascade: ['persist', 'remove'])]
    #[Groups(["offre:read", "offre:write"])]
    private Collection $reservation;

    #[ORM\ManyToOne(inversedBy: 'offres', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["offre:read", "offre:write"])]
    private ?Agency $agency = null;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'offre', cascade: ['persist', 'remove'])]
    #[Groups(["offre:read", "offre:write"])]
    private Collection $review;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'offres', cascade: ['persist'])]
    #[Groups(["offre:read", "offre:write"])]
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'offres', cascade: ['persist'])]
    #[Groups(["offre:read", "offre:write"])]
    private ?Destination $destination = null;

    #[ORM\Column(length: 255)]
    #[Groups(["offre:read", "offre:write"])]
    private ?string $banner = null;

    #[ORM\Column(length: 255)]
    #[Groups(["offre:read", "offre:write"])]
    private ?string $included = null;

    #[ORM\Column(length: 255)]
    #[Groups(["offre:read", "offre:write"])]
    private ?string $no_included = null;

    public function __construct()
    {
        $this->tarifs = new ArrayCollection();
        $this->reservation = new ArrayCollection();
        $this->review = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation->add($reservation);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        $this->reservation->removeElement($reservation);

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

    /**
     * @return Collection<int, Review>
     */
    public function getReview(): Collection
    {
        return $this->review;
    }

    public function addReview(Review $review): static
    {
        if (!$this->review->contains($review)) {
            $this->review->add($review);
            $review->setOffre($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->review->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getOffre() === $this) {
                $review->setOffre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getDestination(): ?destination
    {
        return $this->destination;
    }

    public function setDestination(?destination $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(string $banner): static
    {
        $this->banner = $banner;

        return $this;
    }

    public function getIncluded(): ?string
    {
        return $this->included;
    }

    public function setIncluded(string $included): static
    {
        $this->included = $included;

        return $this;
    }

    public function getNoIncluded(): ?string
    {
        return $this->no_included;
    }

    public function setNoIncluded(string $no_included): static
    {
        $this->no_included = $no_included;

        return $this;
    }
}
