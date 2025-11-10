<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TitleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TitleRepository::class)]
#[ORM\Table(name: 'titles')]
class Title
{
    #[ORM\Id]
    #[ORM\Column(name: 'title_id', type: Types::STRING, length: 6, unique: true)]
    #[Assert\NotBlank(message: 'Title ID cannot be blank.')]
    #[Assert\Length(
        min: 6,
        max: 6,
        exactMessage: 'Title ID must be exactly {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2}[0-9]{4}$/',
        message: 'Title ID must follow the pattern: 2 uppercase letters followed by 4 digits (e.g., BU1032).'
    )]
    private string $titleId;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 80)]
    #[Assert\NotBlank(message: 'Title cannot be blank.')]
    #[Assert\Length(
        min: 1,
        max: 80,
        maxMessage: 'Title cannot be longer than {{ limit }} characters.'
    )]
    private string $title;

    #[ORM\Column(name: 'type', type: Types::STRING, length: 12, options: ['default' => 'UNDECIDED'])]
    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['business', 'mod_cook', 'popular_comp', 'psychology', 'trad_cook', 'UNDECIDED'],
        message: 'Choose a valid type.'
    )]
    private string $type = 'UNDECIDED';

    #[ORM\ManyToOne(targetEntity: Publisher::class)]
    #[ORM\JoinColumn(name: 'pub_id', referencedColumnName: 'pub_id', nullable: true)]
    private ?Publisher $publisher = null;

    #[ORM\Column(name: 'price', type: Types::DECIMAL, precision: 19, scale: 4, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Price must be zero or positive.')]
    private ?string $price = null;

    #[ORM\Column(name: 'advance', type: Types::DECIMAL, precision: 19, scale: 4, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Advance must be zero or positive.')]
    private ?string $advance = null;

    #[ORM\Column(name: 'royalty', type: Types::INTEGER, nullable: true)]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: 'Royalty must be between {{ min }} and {{ max }}.'
    )]
    private ?int $royalty = null;

    #[ORM\Column(name: 'ytd_sales', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Year-to-date sales must be zero or positive.')]
    private ?int $ytdSales = null;

    #[ORM\Column(name: 'notes', type: Types::STRING, length: 200, nullable: true)]
    #[Assert\Length(
        max: 200,
        maxMessage: 'Notes cannot be longer than {{ limit }} characters.'
    )]
    private ?string $notes = null;

    #[ORM\Column(name: 'pubdate', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\NotNull]
    private \DateTimeInterface $pubdate;

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
