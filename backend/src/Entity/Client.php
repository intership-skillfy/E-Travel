<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $profilePic = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $preferences = null;

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
}
