<?php
namespace App\Entity;

use App\Repository\HikingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HikingRepository::class)]
class Hiking extends Offre
{
    #[ORM\Column(length: 255)]
    #[Groups(["offre:read", "offre:write"])]
    private ?string $difficulty = null;

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }
}
