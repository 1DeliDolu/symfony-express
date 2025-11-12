<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\ProductsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
#[ORM\Table(name: "Products", schema: "dbo")]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ProductID", type: "integer")]
    private ?int $productId = null;

    #[ORM\Column(name: "ProductName", length: 40)]
    private ?string $productName = null;

    #[ORM\Column(name: "SupplierID", type: "integer", nullable: true)]
    private ?int $supplierId = null;

    #[ORM\Column(name: "CategoryID", type: "integer", nullable: true)]
    private ?int $categoryId = null;

    #[ORM\Column(name: "QuantityPerUnit", length: 20, nullable: true)]
    private ?string $quantityPerUnit = null;

    #[ORM\Column(name: "UnitPrice", type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $unitPrice = null;

    #[ORM\Column(name: "UnitsInStock", type: "smallint", nullable: true)]
    private ?int $unitsInStock = null;

    #[ORM\Column(name: "UnitsOnOrder", type: "smallint", nullable: true)]
    private ?int $unitsOnOrder = null;

    #[ORM\Column(name: "ReorderLevel", type: "smallint", nullable: true)]
    private ?int $reorderLevel = null;

    #[ORM\Column(name: "Discontinued", type: Types::BOOLEAN)]
    private bool $discontinued = false;

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getSupplierId(): ?int
    {
        return $this->supplierId;
    }

    public function setSupplierId(?int $supplierId): static
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): static
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getQuantityPerUnit(): ?string
    {
        return $this->quantityPerUnit;
    }

    public function setQuantityPerUnit(?string $quantityPerUnit): static
    {
        $this->quantityPerUnit = $quantityPerUnit;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getUnitsInStock(): ?int
    {
        return $this->unitsInStock;
    }

    public function setUnitsInStock(?int $unitsInStock): static
    {
        $this->unitsInStock = $unitsInStock;

        return $this;
    }

    public function getUnitsOnOrder(): ?int
    {
        return $this->unitsOnOrder;
    }

    public function setUnitsOnOrder(?int $unitsOnOrder): static
    {
        $this->unitsOnOrder = $unitsOnOrder;

        return $this;
    }

    public function getReorderLevel(): ?int
    {
        return $this->reorderLevel;
    }

    public function setReorderLevel(?int $reorderLevel): static
    {
        $this->reorderLevel = $reorderLevel;

        return $this;
    }

    public function isDiscontinued(): bool
    {
        return $this->discontinued;
    }

    public function setDiscontinued(bool $discontinued): static
    {
        $this->discontinued = $discontinued;

        return $this;
    }
}
