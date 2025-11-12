<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\SuppliersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuppliersRepository::class)]
#[ORM\Table(name: "Suppliers", schema: "dbo")]
class Suppliers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "SupplierID", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "CompanyName", length: 40)]
    private ?string $companyName = null;

    #[ORM\Column(name: "ContactName", length: 30, nullable: true)]
    private ?string $contactName = null;

    #[ORM\Column(name: "ContactTitle", length: 30, nullable: true)]
    private ?string $contactTitle = null;

    #[ORM\Column(name: "Address", length: 60, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(name: "City", length: 15, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: "Region", length: 15, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(name: "PostalCode", length: 10, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(name: "Country", length: 15, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(name: "Phone", length: 24, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(name: "Fax", length: 24, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(name: "HomePage", type: Types::TEXT, nullable: true)]
    private ?string $homePage = null;

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

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(?string $contactName): static
    {
        $this->contactName = $contactName;

        return $this;
    }

    public function getContactTitle(): ?string
    {
        return $this->contactTitle;
    }

    public function setContactTitle(?string $contactTitle): static
    {
        $this->contactTitle = $contactTitle;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

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

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): static
    {
        $this->fax = $fax;

        return $this;
    }

    public function getHomePage(): ?string
    {
        return $this->homePage;
    }

    public function setHomePage(?string $homePage): static
    {
        $this->homePage = $homePage;

        return $this;
    }
}
