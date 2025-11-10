<?php

namespace App\Entity;

use App\Repository\StoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
#[ORM\Table(name: 'stores')]
class Store
{
    #[ORM\Id]
    #[ORM\Column(name: 'stor_id', type: 'string', length: 4, unique: true)]
    private string $storId;

    #[ORM\Column(name: 'stor_name', type: 'string', length: 40, nullable: true)]
    private ?string $storName = null;

    #[ORM\Column(name: 'stor_address', type: 'string', length: 40, nullable: true)]
    private ?string $storAddress = null;

    #[ORM\Column(name: 'city', type: 'string', length: 20, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: 'state', type: 'string', length: 2, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(name: 'zip', type: 'string', length: 5, nullable: true)]
    private ?string $zip = null;

    // 🧩 Getter / Setter Metodları

    public function getStorId(): string
    {
        return $this->storId;
    }

    public function setStorId(string $storId): self
    {
        $this->storId = $storId;
        return $this;
    }

    public function getStorName(): ?string
    {
        return $this->storName;
    }

    public function setStorName(?string $storName): self
    {
        $this->storName = $storName;
        return $this;
    }

    public function getStorAddress(): ?string
    {
        return $this->storAddress;
    }

    public function setStorAddress(?string $storAddress): self
    {
        $this->storAddress = $storAddress;
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
}
