<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $auId = null;

    #[ORM\Column(length: 255)]
    private ?string $auLname = null;

    #[ORM\Column(length: 255)]
    private ?string $auFname = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column]
    private ?bool $contract = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuId(): ?string
    {
        return $this->auId;
    }

    public function setAuId(string $auId): static
    {
        $this->auId = $auId;

        return $this;
    }

    public function getAuLname(): ?string
    {
        return $this->auLname;
    }

    public function setAuLname(string $auLname): static
    {
        $this->auLname = $auLname;

        return $this;
    }

    public function getAuFname(): ?string
    {
        return $this->auFname;
    }

    public function setAuFname(string $auFname): static
    {
        $this->auFname = $auFname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): static
    {
        $this->zip = $zip;

        return $this;
    }

    public function isContract(): ?bool
    {
        return $this->contract;
    }

    public function setContract(bool $contract): static
    {
        $this->contract = $contract;

        return $this;
    }
}
