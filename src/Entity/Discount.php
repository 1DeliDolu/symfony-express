<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DiscountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
#[ORM\Table(name: 'discounts')]
class Discount
{
    // Kimliğin 1. parçası: discounttype
    #[ORM\Id]
    #[ORM\Column(name: 'discounttype', type: Types::STRING, length: 40)]
    #[Assert\NotBlank(message: 'İndirim tipi boş olamaz')]
    #[Assert\Length(max: 40, maxMessage: 'İndirim tipi en fazla {{ limit }} karakter olabilir')]
    private string $discountType;

    // Kimliğin 2. parçası: stor_id (nullable FK)
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Store::class)]
    #[ORM\JoinColumn(name: 'stor_id', referencedColumnName: 'stor_id', nullable: true)]
    private ?Store $store = null;

    #[ORM\Column(name: 'lowqty', type: Types::SMALLINT, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Minimum miktar 0 veya daha büyük olmalıdır')]
    private ?int $lowQty = null;

    #[ORM\Column(name: 'highqty', type: Types::SMALLINT, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Maximum miktar 0 veya daha büyük olmalıdır')]
    private ?int $highQty = null;

    // SQL Server DEC(4,2) → Doctrine decimal(4,2)
    #[ORM\Column(name: 'discount', type: Types::DECIMAL, precision: 4, scale: 2)]
    #[Assert\NotBlank(message: 'İndirim oranı boş olamaz')]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: 'İndirim oranı {{ min }} ile {{ max }} arasında olmalıdır')]
    private string $discount;

    // --------- Getter / Setter ---------

    public function getDiscountType(): string
    {
        return $this->discountType;
    }

    public function setDiscountType(string $discountType): self
    {
        $this->discountType = $discountType;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getLowQty(): ?int
    {
        return $this->lowQty;
    }

    public function setLowQty(?int $lowQty): self
    {
        $this->lowQty = $lowQty;

        return $this;
    }

    public function getHighQty(): ?int
    {
        return $this->highQty;
    }

    public function setHighQty(?int $highQty): self
    {
        $this->highQty = $highQty;

        return $this;
    }

    public function getDiscount(): string
    {
        return $this->discount;
    }

    public function setDiscount(string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }
}
