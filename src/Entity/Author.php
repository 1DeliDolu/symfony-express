<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\Table(name: 'authors')]
class Author
{
    #[ORM\Id]
    #[ORM\Column(name: 'au_id', type: Types::STRING, length: 11, unique: true)]
    #[Assert\NotBlank(message: 'Author ID cannot be blank.')]
    #[Assert\Length(
        min: 11,
        max: 11,
        exactMessage: 'Author ID must be exactly {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^\d{3}-\d{2}-\d{4}$/',
        message: 'Author ID must follow the pattern: XXX-XX-XXXX (e.g., 123-45-6789).'
    )]
    private string $auId;

    #[ORM\Column(name: 'au_lname', type: Types::STRING, length: 40)]
    #[Assert\NotBlank(message: 'Last name cannot be blank.')]
    #[Assert\Length(
        min: 1,
        max: 40,
        maxMessage: 'Last name cannot be longer than {{ limit }} characters.'
    )]
    private string $auLname;

    #[ORM\Column(name: 'au_fname', type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: 'First name cannot be blank.')]
    #[Assert\Length(
        min: 1,
        max: 20,
        maxMessage: 'First name cannot be longer than {{ limit }} characters.'
    )]
    private string $auFname;

    #[ORM\Column(
        name: 'phone',
        type: Types::STRING,
        length: 12,
        options: ['default' => 'UNKNOWN']
    )]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{3} \d{3}-\d{4}$|^UNKNOWN$/',
        message: 'Phone must follow the pattern: XXX XXX-XXXX or be "UNKNOWN".'
    )]
    private string $phone = 'UNKNOWN';

    #[ORM\Column(name: 'address', type: Types::STRING, length: 40, nullable: true)]
    #[Assert\Length(max: 40)]
    private ?string $address = null;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $city = null;

    #[ORM\Column(name: 'state', type: Types::STRING, length: 2, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 2,
        exactMessage: 'State must be exactly {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2}$/',
        message: 'State must be 2 uppercase letters.'
    )]
    private ?string $state = null;

    #[ORM\Column(name: 'zip', type: Types::STRING, length: 5, nullable: true)]
    #[Assert\Length(
        min: 5,
        max: 5,
        exactMessage: 'ZIP code must be exactly {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^\d{5}$/',
        message: 'ZIP code must be 5 digits.'
    )]
    private ?string $zip = null;

    #[ORM\Column(name: 'contract', type: Types::BOOLEAN)]
    #[Assert\NotNull]
    private bool $contract;

    public function getAuId(): string
    {
        return $this->auId;
    }

    public function setAuId(string $auId): self
    {
        $this->auId = $auId;
        return $this;
    }

    public function getAuLname(): string
    {
        return $this->auLname;
    }

    public function setAuLname(string $auLname): self
    {
        $this->auLname = $auLname;
        return $this;
    }

    public function getAuFname(): string
    {
        return $this->auFname;
    }

    public function setAuFname(string $auFname): self
    {
        $this->auFname = $auFname;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;
        return $this;
    }

    public function isContract(): bool
    {
        return $this->contract;
    }

    public function setContract(bool $contract): self
    {
        $this->contract = $contract;
        return $this;
    }
}
