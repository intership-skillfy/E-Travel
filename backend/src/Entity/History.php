<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $searchHistory = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearchHistory(): ?array
    {
        return $this->searchHistory;
    }

    public function setSearchHistory(?array $searchHistory): static
    {
        $this->searchHistory = $searchHistory;

        return $this;
    }
}
