<?php

namespace App\Entity;

use App\Repository\TitleRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Publisher;

#[ORM\Entity(repositoryClass: TitleRepository::class)]
#[ORM\Table(name: 'titles')]
class Title
{
    #[ORM\Id]
    #[ORM\Column(name: 'title_id', type: 'string', length: 6, unique: true)]
    private string $titleId;

    #[ORM\Column(name: 'title', type: 'string', length: 80)]
    private string $title;

    #[ORM\Column(name: 'type', type: 'string', length: 12, options: ['default' => 'UNDECIDED'])]
    private string $type = 'UNDECIDED';

    #[ORM\ManyToOne(targetEntity: Publisher::class)]
    #[ORM\JoinColumn(name: 'pub_id', referencedColumnName: 'pub_id', nullable: true)]
    private ?Publisher $publisher = null;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 19, scale: 4, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(name: 'advance', type: 'decimal', precision: 19, scale: 4, nullable: true)]
    private ?string $advance = null;

    #[ORM\Column(name: 'royalty', type: 'integer', nullable: true)]
    private ?int $royalty = null;

    #[ORM\Column(name: 'ytd_sales', type: 'integer', nullable: true)]
    private ?int $ytdSales = null;

    #[ORM\Column(name: 'notes', type: 'string', length: 200, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: 'pubdate', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $pubdate;

    // 🧩 Getter / Setter Metodları

    public function getTitleId(): string
    {
        return $this->titleId;
    }

    public function setTitleId(string $titleId): self
    {
        $this->titleId = $titleId;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPublisher(): ?Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(?Publisher $publisher): self
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getAdvance(): ?string
    {
        return $this->advance;
    }

    public function setAdvance(?string $advance): self
    {
        $this->advance = $advance;
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

    public function getYtdSales(): ?int
    {
        return $this->ytdSales;
    }

    public function setYtdSales(?int $ytdSales): self
    {
        $this->ytdSales = $ytdSales;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getPubdate(): \DateTimeInterface
    {
        return $this->pubdate;
    }

    public function setPubdate(\DateTimeInterface $pubdate): self
    {
        $this->pubdate = $pubdate;
        return $this;
    }
}
