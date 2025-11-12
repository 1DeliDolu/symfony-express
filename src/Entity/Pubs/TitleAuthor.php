<?php

declare(strict_types=1);

namespace App\Entity\Pubs;

use App\Repository\Pubs\TitleAuthorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TitleAuthorRepository::class)]
#[ORM\Table(name: 'titleauthor')]
class TitleAuthor
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(name: 'au_id', referencedColumnName: 'au_id')]
    #[Assert\NotNull(message: 'Yazar seçilmelidir')]
    private Author $author;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: 'title_id', referencedColumnName: 'title_id')]
    #[Assert\NotNull(message: 'Kitap seçilmelidir')]
    private Title $title;

    #[ORM\Column(name: 'au_ord', type: Types::SMALLINT, nullable: true)]
    #[Assert\Positive(message: 'Yazar sırası pozitif olmalıdır')]
    private ?int $auOrd = null;

    #[ORM\Column(name: 'royaltyper', type: Types::INTEGER, nullable: true)]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: 'Telif hakkı yüzdesi {{ min }} ile {{ max }} arasında olmalıdır')]
    private ?int $royaltyPer = null;

    // 🧩 Getter / Setter Metodları

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function setTitle(Title $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuOrd(): ?int
    {
        return $this->auOrd;
    }

    public function setAuOrd(?int $auOrd): self
    {
        $this->auOrd = $auOrd;

        return $this;
    }

    public function getRoyaltyPer(): ?int
    {
        return $this->royaltyPer;
    }

    public function setRoyaltyPer(?int $royaltyPer): self
    {
        $this->royaltyPer = $royaltyPer;

        return $this;
    }
}
