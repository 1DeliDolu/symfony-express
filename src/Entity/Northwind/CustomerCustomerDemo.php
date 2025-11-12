<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\CustomerCustomerDemoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerCustomerDemoRepository::class)]
#[ORM\Table(name: "CustomerCustomerDemo", schema: "dbo")]
class CustomerCustomerDemo
{
    // This table uses a composite primary key (CustomerID, CustomerTypeID)
    #[ORM\Id]
    #[ORM\Column(name: "CustomerID", length: 5)]
    private ?string $customerId = null;

    #[ORM\Id]
    #[ORM\Column(name: "CustomerTypeID", length: 10)]
    private ?string $customerTypeId = null;

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomerTypeId(): ?string
    {
        return $this->customerTypeId;
    }

    public function setCustomerTypeId(string $customerTypeId): static
    {
        $this->customerTypeId = $customerTypeId;

        return $this;
    }
}
