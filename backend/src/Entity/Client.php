<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    
    #[Groups("full")]
    #[ORM\Column(length: 255)]
    private ?string $profilePic = null;

    #[Groups("full")]
    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[Groups("full")]
    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $preferences = null;

     /**
     * @var Collection<int, GuideArticle>
     */
    #[Groups("full")]
    #[ORM\OneToMany(targetEntity: GuideArticle::class, mappedBy: 'agent')]
    private Collection $article;

    /**
     * @var Collection<int, Reservation>
     */
    #[Groups("full")]
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'client')]
    private Collection $reservations;

    /**
     * @var Collection<int, Review>
     */
    #[Groups("full")]
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'client')]
    private Collection $reviews;

    #[Groups("full")]
    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?Pack $pack = null;

    #[Groups("full")]
    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?History $history = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfilePic(): ?string
    {
        return $this->profilePic;
    }

    public function setProfilePic(string $profilePic): static
    {
        $this->profilePic = $profilePic;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPreferences(): ?array
    {
        return $this->preferences;
    }

    public function setPreferences(?array $preferences): static
    {
        $this->preferences = $preferences;

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
            $reservation->setClient($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getClient() === $this) {
                $reservation->setClient(null);
            }
        }

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
            $review->setClient($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getClient() === $this) {
                $review->setClient(null);
            }
        }

        return $this;
    }

    public function getPack(): ?Pack
    {
        return $this->pack;
    }

    public function setPack(Pack $pack): static
    {
        // set the owning side of the relation if necessary
        if ($pack->getClient() !== $this) {
            $pack->setClient($this);
        }

        $this->pack = $pack;

        return $this;
    }

    public function getHistory(): ?History
    {
        return $this->history;
    }

    public function setHistory(History $history): static
    {
        // set the owning side of the relation if necessary
        if ($history->getClient() !== $this) {
            $history->setClient($this);
        }

        $this->history = $history;

        return $this;
    }
}
