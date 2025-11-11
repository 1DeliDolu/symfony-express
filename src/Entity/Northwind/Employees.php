<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\EmployeesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeesRepository::class)]
#[ORM\Table(name: "Employees")]
class Employees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "EmployeeID", type: "integer")]
    private ?int $employeeID = null;

    #[ORM\Column(name: "LastName", type: "string", length: 20)]
    private string $lastName;

    #[ORM\Column(name: "FirstName", type: "string", length: 10)]
    private string $firstName;

    #[ORM\Column(name: "Title", type: "string", length: 30, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(name: "TitleOfCourtesy", type: "string", length: 25, nullable: true)]
    private ?string $titleOfCourtesy = null;

    #[ORM\Column(name: "BirthDate", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(name: "HireDate", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $hireDate = null;

    #[ORM\Column(name: "Address", type: "string", length: 60, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(name: "City", type: "string", length: 15, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: "Region", type: "string", length: 15, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(name: "PostalCode", type: "string", length: 10, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(name: "Country", type: "string", length: 15, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(name: "HomePhone", type: "string", length: 24, nullable: true)]
    private ?string $homePhone = null;

    #[ORM\Column(name: "Extension", type: "string", length: 4, nullable: true)]
    private ?string $extension = null;

    #[ORM\Column(name: "Photo", type: "blob", nullable: true)]
    private $photo = null;

    #[ORM\Column(name: "Notes", type: "text", nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: "ReportsTo", referencedColumnName: "EmployeeID", nullable: true)]
    private ?self $reportsTo = null;

    #[ORM\Column(name: "PhotoPath", type: "string", length: 255, nullable: true)]
    private ?string $photoPath = null;

    // --- GETTER & SETTER METHODS ---

    public function getEmployeeID(): ?int
    {
        return $this->employeeID;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitleOfCourtesy(): ?string
    {
        return $this->titleOfCourtesy;
    }

    public function setTitleOfCourtesy(?string $titleOfCourtesy): self
    {
        $this->titleOfCourtesy = $titleOfCourtesy;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }

    public function setHireDate(?\DateTimeInterface $hireDate): self
    {
        $this->hireDate = $hireDate;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    public function setHomePhone(?string $homePhone): self
    {
        $this->homePhone = $homePhone;
        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getReportsTo(): ?self
    {
        return $this->reportsTo;
    }

    public function setReportsTo(?self $reportsTo): self
    {
        $this->reportsTo = $reportsTo;
        return $this;
    }

    public function getPhotoPath()
    {
        return $this->photoPath;
    }

    public function setPhotoPath(?string $photoPath): self
    {
        $this->photoPath = $photoPath;
        return $this;
    }
}
