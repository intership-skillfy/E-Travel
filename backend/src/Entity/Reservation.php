<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attributes\Groups as AttributeGroups ;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["full", "offre:read", "offre:write"])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["full", "offre:read", "offre:write"])]
    private ?\DateTimeImmutable $reservationDate = null;

    #[ORM\Column]
    #[Groups(["full", "offre:read", "offre:write"])]
    private ?float $amount = null;

    #[ORM\Column(length: 255)]
    #[Groups(["full", "offre:read", "offre:write"])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(["offre:read", "offre:write"])]
    private ?Client $client = null;

    #[ORM\Column]
    #[Groups(["offre:read", "offre:write"])]
    private ?int $nbr_person = null;

    #[ORM\ManyToOne(inversedBy: 'reservation')]
    private ?History $history = null;

    /**
     * @var Collection<int, Offre>
     */
    #[ORM\ManyToMany(targetEntity: Offre::class, mappedBy: 'reservation')]
    private Collection $offres;

    public function __construct()
    {
        $this->offres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservationDate(): ?\DateTimeImmutable
    {
        return $this->reservationDate;
    }

    public function setReservationDate(\DateTimeImmutable $reservationDate): static
    {
        $this->reservationDate = $reservationDate;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getNbrPerson(): ?int
    {
        return $this->nbr_person;
    }

    public function setNbrPerson(int $nbr_person): static
    {
        $this->nbr_person = $nbr_person;

        return $this;
    }

    public function getHistory(): ?History
    {
        return $this->history;
    }

    public function setHistory(?History $history): static
    {
        $this->history = $history;

        return $this;
    }

    /**
     * @return Collection<int, Offre>
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offre $offre): static
    {
        if (!$this->offres->contains($offre)) {
            $this->offres->add($offre);
            $offre->addReservation($this);
        }

        return $this;
    }

    public function removeOffre(Offre $offre): static
    {
        if ($this->offres->removeElement($offre)) {
            $offre->removeReservation($this);
        }

        return $this;
    }
}
