<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\ShippersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShippersRepository::class)]
#[ORM\Table(name: "Shippers", schema: "dbo")]
class Shippers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ShipperID", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "CompanyName", length: 40)]
    private ?string $companyName = null;

    #[ORM\Column(name: "Phone", length: 24, nullable: true)]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
