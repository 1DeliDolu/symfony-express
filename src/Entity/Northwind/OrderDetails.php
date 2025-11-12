<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\OrderDetailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDetailsRepository::class)]
#[ORM\Table(name: "OrderDetails", schema: "dbo")]
class OrderDetails
{
    // Composite PK: OrderID + ProductID
    #[ORM\Id]
    #[ORM\Column(name: "OrderID", type: "integer")]
    private ?int $orderId = null;

    #[ORM\Id]
    #[ORM\Column(name: "ProductID", type: "integer")]
    private ?int $productId = null;

    #[ORM\Column(name: "UnitPrice", type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $unitPrice = null;

    #[ORM\Column(name: "Quantity", type: "smallint")]
    private ?int $quantity = null;

    #[ORM\Column(name: "Discount", type: Types::FLOAT)]
    private ?float $discount = null;

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): static
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }
}
