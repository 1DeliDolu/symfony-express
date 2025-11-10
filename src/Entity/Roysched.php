<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Title;

#[ORM\Entity(repositoryClass: 'App\Repository\RoyschedRepository')]
#[ORM\Table(name: 'roysched')]
class Roysched
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: 'title_id', referencedColumnName: 'title_id', nullable: false)]
    private Title $title;

    #[ORM\Column(name: 'lorange', type: 'integer', nullable: true)]
    private ?int $lorange = null;

    #[ORM\Column(name: 'hirange', type: 'integer', nullable: true)]
    private ?int $hirange = null;

    #[ORM\Column(name: 'royalty', type: 'integer', nullable: true)]
    private ?int $royalty = null;

    // 🧩 Getter / Setter Metodları

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function setTitle(Title $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getLorange(): ?int
    {
        return $this->lorange;
    }

    public function setLorange(?int $lorange): self
    {
        $this->lorange = $lorange;
        return $this;
    }

    public function getHirange(): ?int
    {
        return $this->hirange;
    }

    public function setHirange(?int $hirange): self
    {
        $this->hirange = $hirange;
        return $this;
    }

    public function getRoyalty(): ?int
    {
        return $this->royalty;
    }

    public function setRoyalty(?int $royalty): self
    {
        $this->royalty = $royalty;
        return $this;
    }
}
