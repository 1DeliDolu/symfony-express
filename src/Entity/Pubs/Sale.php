<?php

declare(strict_types=1);

namespace App\Entity\Pubs;

use App\Repository\Pubs\SaleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ORM\Table(name: 'sales')]
class Sale
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Store::class)]
    #[ORM\JoinColumn(name: 'stor_id', referencedColumnName: 'stor_id', nullable: false)]
    #[Assert\NotNull(message: 'Mağaza seçilmelidir')]
    private Store $store;

    #[ORM\Id]
    #[ORM\Column(name: 'ord_num', type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: 'Sipariş numarası boş olamaz')]
    #[Assert\Length(max: 20, maxMessage: 'Sipariş numarası en fazla {{ limit }} karakter olabilir')]
    private string $ordNum;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: 'title_id', referencedColumnName: 'title_id', nullable: false)]
    #[Assert\NotNull(message: 'Kitap seçilmelidir')]
    private Title $title;

    #[ORM\Column(name: 'ord_date', type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'Sipariş tarihi boş olamaz')]
    private \DateTimeInterface $ordDate;

    #[ORM\Column(name: 'qty', type: Types::SMALLINT)]
    #[Assert\NotNull(message: 'Miktar boş olamaz')]
    #[Assert\Positive(message: 'Miktar pozitif olmalıdır')]
    private int $qty;

    #[ORM\Column(name: 'payterms', type: Types::STRING, length: 12)]
    #[Assert\NotBlank(message: 'Ödeme koşulları boş olamaz')]
    #[Assert\Length(max: 12, maxMessage: 'Ödeme koşulları en fazla {{ limit }} karakter olabilir')]
    private string $payterms;

    // 🧩 Getter / Setter Metodları

    public function getStore(): Store
    {
        return $this->store;
    }

    public function setStore(Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getOrdNum(): string
    {
        return $this->ordNum;
    }

    public function setOrdNum(string $ordNum): self
    {
        $this->ordNum = $ordNum;

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

    public function getOrdDate(): \DateTimeInterface
    {
        return $this->ordDate;
    }

    public function setOrdDate(\DateTimeInterface $ordDate): self
    {
        $this->ordDate = $ordDate;

        return $this;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getPayterms(): string
    {
        return $this->payterms;
    }

    public function setPayterms(string $payterms): self
    {
        $this->payterms = $payterms;

        return $this;
    }
}
