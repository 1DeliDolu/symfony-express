<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StoreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
#[ORM\Table(name: 'stores')]
class Store
{
    #[ORM\Id]
    #[ORM\Column(name: 'stor_id', type: Types::STRING, length: 4, unique: true)]
    #[Assert\NotBlank(message: 'Mağaza ID boş olamaz')]
    #[Assert\Length(exactly: 4, exactMessage: 'Mağaza ID tam {{ limit }} karakter olmalıdır')]
    private string $storId;

    #[ORM\Column(name: 'stor_name', type: Types::STRING, length: 40, nullable: true)]
    #[Assert\Length(max: 40, maxMessage: 'Mağaza adı en fazla {{ limit }} karakter olabilir')]
    private ?string $storName = null;

    #[ORM\Column(name: 'stor_address', type: Types::STRING, length: 40, nullable: true)]
    #[Assert\Length(max: 40, maxMessage: 'Adres en fazla {{ limit }} karakter olabilir')]
    private ?string $storAddress = null;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: 'Şehir en fazla {{ limit }} karakter olabilir')]
    private ?string $city = null;

    #[ORM\Column(name: 'state', type: Types::STRING, length: 2, nullable: true)]
    #[Assert\Length(exactly: 2, exactMessage: 'Eyalet kodu tam {{ limit }} karakter olmalıdır')]
    #[Assert\Regex(pattern: '/^[A-Z]{2}$/', message: 'Eyalet kodu 2 büyük harf olmalıdır')]
    private ?string $state = null;

    #[ORM\Column(name: 'zip', type: Types::STRING, length: 5, nullable: true)]
    #[Assert\Length(exactly: 5, exactMessage: 'Posta kodu tam {{ limit }} karakter olmalıdır')]
    #[Assert\Regex(pattern: '/^\d{5}$/', message: 'Posta kodu 5 rakam olmalıdır')]
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

    public function __toString(): string
    {
        return $this->storName ?? $this->storId;
    }
}
