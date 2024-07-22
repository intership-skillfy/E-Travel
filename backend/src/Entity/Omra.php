<?php

namespace App\Entity;

use App\Repository\OmraRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OmraRepository::class)]
class Omra extends Offre
{
  
}
