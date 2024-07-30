<?php

namespace App\Entity;

use App\Repository\GuideArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: GuideArticleRepository::class)]
class GuideArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups("full")]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups("full")]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[Groups("full")]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups("full")]
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Groups("full")]
    #[ORM\Column(type: Types::ARRAY)]
    private array $images = [];

    #[Groups("full")]
    #[ORM\ManyToOne(inversedBy: 'article')]
    private ?Agency $agency = null;

    #[Groups("full")]
    #[ORM\ManyToOne(inversedBy: 'article')]
    private ?Admin $admin_article = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): static
    {
        $this->images = $images;

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

    public function getAdminArticle(): ?Admin
    {
        return $this->admin_article;
    }

    public function setAdminArticle(?Admin $admin_article): static
    {
        $this->admin_article = $admin_article;

        return $this;
    }


}
