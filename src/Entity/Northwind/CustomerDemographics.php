<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\CustomerDemographicsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerDemographicsRepository::class)]
#[ORM\Table(name: "CustomerDemographics", schema: "dbo")]
class CustomerDemographics
{
    #[ORM\Id]
    #[ORM\Column(name: "CustomerTypeID", length: 10)]
    private ?string $customerTypeId = null;

    #[ORM\Column(name: "CustomerDesc", type: Types::TEXT, nullable: true)]
    private ?string $customerDesc = null;

    public function getCustomerTypeId(): ?string
    {
        return $this->customerTypeId;
    }

    public function setCustomerTypeId(string $customerTypeId): static
    {
        $this->customerTypeId = $customerTypeId;

        return $this;
    }

    public function getCustomerDesc(): ?string
    {
        return $this->customerDesc;
    }

    public function setCustomerDesc(?string $customerDesc): static
    {
        $this->customerDesc = $customerDesc;

        return $this;
    }
}
