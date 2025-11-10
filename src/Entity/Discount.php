<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Store;

#[ORM\Entity(repositoryClass: 'App\Repository\DiscountRepository')]
#[ORM\Table(name: 'discounts')]
class Discount
{
    // Kimliğin 1. parçası: discounttype
    #[ORM\Id]
    #[ORM\Column(name: 'discounttype', type: 'string', length: 40)]
    private string $discountType;

    // Kimliğin 2. parçası: stor_id (nullable FK)
    // Not: DB şemasında NULL olabildiği için burada da nullable=true bırakıldı.
    // Doctrine kimlik alanlarında null istenmez; bu durum varsa bazı ORM işlemleri sınırlı çalışabilir.
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Store::class)]
    #[ORM\JoinColumn(name: 'stor_id', referencedColumnName: 'stor_id', nullable: true)]
    private ?Store $store = null;

    #[ORM\Column(name: 'lowqty', type: 'smallint', nullable: true)]
    private ?int $lowQty = null;

    #[ORM\Column(name: 'highqty', type: 'smallint', nullable: true)]
    private ?int $highQty = null;

    // SQL Server DEC(4,2) → Doctrine decimal(4,2)
    #[ORM\Column(name: 'discount', type: 'decimal', precision: 4, scale: 2)]
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
