<?php

namespace App\Entity;

use App\Repository\ExcursionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExcursionRepository::class)]
class Excursion extends Offre
{

    #[ORM\Column]
    private ?bool $extra = null;

 

    public function isExtra(): ?bool
    {
        return $this->extra;
    }

    public function setExtra(bool $extra): static
    {
        $this->extra = $extra;

        return $this;
    }
}
