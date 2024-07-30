<?php

namespace App\Entity;

use App\Repository\PriceListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PriceListRepository::class)]
class PriceList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["offre:read", "offre:write"])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["offre:read", "offre:write"])]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column]
    #[Groups(["offre:read", "offre:write"])]
    private ?\DateTimeImmutable $end_date = null;


    #[ORM\Column]
    #[Groups(["offre:read", "offre:write"])]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'tarifs')]
    private ?Offre $offre = null;

    /**
     * @var Collection<int, hotel>
     */
    #[ORM\ManyToMany(targetEntity: Hotel::class, inversedBy: 'priceLists')]
    private Collection $hotels;

    public function __construct()
    {
        $this->hotels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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


    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): static
    {
        $this->offre = $offre;

        return $this;
    }

    /**
     * @return Collection<int, hotel>
     */
    public function getHotels(): Collection
    {
        return $this->hotels;
    }

    public function addHotel(hotel $hotel): static
    {
        if (!$this->hotels->contains($hotel)) {
            $this->hotels->add($hotel);
        }

        return $this;
    }

    public function removeHotel(hotel $hotel): static
    {
        $this->hotels->removeElement($hotel);

        return $this;
    }
}
