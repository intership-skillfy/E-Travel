<?php

namespace App\Entity;

use App\Repository\ExcursionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExcursionRepository::class)]
class Excursion extends Offre
{

    #[ORM\Column]
    private ?bool $included = null;

 

    public function isIncluded(): ?bool
    {
        return $this->included;
    }

    public function setIncluded(bool $included): static
    {
        $this->included = $included;

        return $this;
    }
}
