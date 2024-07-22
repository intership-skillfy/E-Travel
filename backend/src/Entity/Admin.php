<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table(name: '`admin`')]
class Admin extends User
{
   
 /**
     * @var Collection<int, GuideArticle>
     */
    #[ORM\OneToMany(targetEntity: GuideArticle::class, mappedBy: 'admin_article')]
    private Collection $article;

    public function __construct()
    {
        $this->article = new ArrayCollection();
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
            $article->setAdminArticle($this);
        }

        return $this;
    }

    public function removeArticle(GuideArticle $article): static
    {
        if ($this->article->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAdminArticle() === $this) {
                $article->setAdminArticle(null);
            }
        }

        return $this;
    }
}
