<?php

namespace App\Entity;

use App\Repository\AgencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgencyRepository::class)]
class Agency extends User
{
    #[ORM\GeneratedValue]
    #[ORM\Column]

    #[ORM\Column(length: 255)]
    private ?string $addresse = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255)]
    private ?string $logo_url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(string $addresse): static
    {
        $this->addresse = $addresse;

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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
    }

    public function setLogoUrl(string $logo_url): static
    {
        $this->logo_url = $logo_url;

        return $this;
    }
}
