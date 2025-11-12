<?php

declare(strict_types=1);

namespace App\Entity\Pubs;

use App\Repository\Pubs\RoyschedRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoyschedRepository::class)]
#[ORM\Table(name: 'roysched')]
class Roysched
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: 'title_id', referencedColumnName: 'title_id', nullable: false)]
    #[Assert\NotNull(message: 'Kitap seçilmelidir')]
    private Title $title;

    #[ORM\Column(name: 'lorange', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Alt aralık 0 veya daha büyük olmalıdır')]
    private ?int $lorange = null;

    #[ORM\Column(name: 'hirange', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Üst aralık 0 veya daha büyük olmalıdır')]
    private ?int $hirange = null;

    #[ORM\Column(name: 'royalty', type: Types::INTEGER, nullable: true)]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: 'Telif hakkı oranı {{ min }} ile {{ max }} arasında olmalıdır')]
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
