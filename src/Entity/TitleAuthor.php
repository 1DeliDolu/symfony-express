<?php

namespace App\Entity;

use App\Repository\TitleAuthorRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Author;
use App\Entity\Title;

#[ORM\Entity(repositoryClass: TitleAuthorRepository::class)]
#[ORM\Table(name: 'titleauthor')]
class TitleAuthor
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(name: 'au_id', referencedColumnName: 'au_id')]
    private Author $author;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: 'title_id', referencedColumnName: 'title_id')]
    private Title $title;

    #[ORM\Column(name: 'au_ord', type: 'smallint', nullable: true)]
    private ?int $auOrd = null;

    #[ORM\Column(name: 'royaltyper', type: 'integer', nullable: true)]
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
