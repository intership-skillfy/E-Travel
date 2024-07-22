<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent extends User
{
    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agency $agency = null;

    public function __construct()
    {
        $this->article = new ArrayCollection();
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

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): static
    {
        $this->agency = $agency;

        return $this;
    }

    /**
     * @return Collection<int, GuideArticle>
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(GuideArticle $article): static
    {
        if (!$this->article->contains($article)) {
            $this->article->add($article);
            $article->setAgent($this);
        }

        return $this;
    }

    public function removeArticle(GuideArticle $article): static
    {
        if ($this->article->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAgent() === $this) {
                $article->setAgent(null);
            }
        }

        return $this;
    }
}
