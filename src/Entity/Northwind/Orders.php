<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\OrdersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
#[ORM\Table(name: "Orders", schema: "dbo")]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "OrderID", type: "integer")]
    private ?int $orderId = null;

    #[ORM\Column(name: "CustomerID", length: 5, nullable: true)]
    private ?string $customerId = null;

    #[ORM\Column(name: "EmployeeID", type: "integer", nullable: true)]
    private ?int $employeeId = null;

    #[ORM\Column(name: "OrderDate", type: "datetime", nullable: true)]
    private ?\DateTime $orderDate = null;

    #[ORM\Column(name: "RequiredDate", type: "datetime", nullable: true)]
    private ?\DateTime $requiredDate = null;

    #[ORM\Column(name: "ShippedDate", type: "datetime", nullable: true)]
    private ?\DateTime $shippedDate = null;

    #[ORM\Column(name: "ShipVia", type: "integer", nullable: true)]
    private ?int $shipVia = null;

    #[ORM\Column(name: "Freight", type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $freight = null;

    #[ORM\Column(name: "ShipName", length: 40, nullable: true)]
    private ?string $shipName = null;

    #[ORM\Column(name: "ShipAddress", length: 60, nullable: true)]
    private ?string $shipAddress = null;

    #[ORM\Column(name: "ShipCity", length: 15, nullable: true)]
    private ?string $shipCity = null;

    #[ORM\Column(name: "ShipRegion", length: 15, nullable: true)]
    private ?string $shipRegion = null;

    #[ORM\Column(name: "ShipPostalCode", length: 10, nullable: true)]
    private ?string $shipPostalCode = null;

    #[ORM\Column(name: "ShipCountry", length: 15, nullable: true)]
    private ?string $shipCountry = null;

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function setEmployeeId(?int $employeeId): static
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function getOrderDate(): ?\DateTime
    {
        return $this->orderDate;
    }

    public function setOrderDate(?\DateTime $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getRequiredDate(): ?\DateTime
    {
        return $this->requiredDate;
    }

    public function setRequiredDate(?\DateTime $requiredDate): static
    {
        $this->requiredDate = $requiredDate;

        return $this;
    }

    public function getShippedDate(): ?\DateTime
    {
        return $this->shippedDate;
    }

    public function setShippedDate(?\DateTime $shippedDate): static
    {
        $this->shippedDate = $shippedDate;

        return $this;
    }

    public function getShipVia(): ?int
    {
        return $this->shipVia;
    }

    public function setShipVia(?int $shipVia): static
    {
        $this->shipVia = $shipVia;

        return $this;
    }

    public function getFreight(): ?string
    {
        return $this->freight;
    }

    public function setFreight(?string $freight): static
    {
        $this->freight = $freight;

        return $this;
    }

    public function getShipName(): ?string
    {
        return $this->shipName;
    }

    public function setShipName(?string $shipName): static
    {
        $this->shipName = $shipName;

        return $this;
    }

    public function getShipAddress(): ?string
    {
        return $this->shipAddress;
    }

    public function setShipAddress(?string $shipAddress): static
    {
        $this->shipAddress = $shipAddress;

        return $this;
    }

    public function getShipCity(): ?string
    {
        return $this->shipCity;
    }

    public function setShipCity(?string $shipCity): static
    {
        $this->shipCity = $shipCity;

        return $this;
    }

    public function getShipRegion(): ?string
    {
        return $this->shipRegion;
    }

    public function setShipRegion(?string $shipRegion): static
    {
        $this->shipRegion = $shipRegion;

        return $this;
    }

    public function getShipPostalCode(): ?string
    {
        return $this->shipPostalCode;
    }

    public function setShipPostalCode(?string $shipPostalCode): static
    {
        $this->shipPostalCode = $shipPostalCode;

        return $this;
    }

    public function getShipCountry(): ?string
    {
        return $this->shipCountry;
    }

    public function setShipCountry(?string $shipCountry): static
    {
        $this->shipCountry = $shipCountry;

        return $this;
    }
}
