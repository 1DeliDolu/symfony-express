<?php

declare(strict_types=1);

namespace App\Entity\Pubs;

use App\Repository\Pubs\PublisherRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PublisherRepository::class)]
#[ORM\Table(name: 'publishers')]
class Publisher
{
    #[ORM\Id]
    #[ORM\Column(name: 'pub_id', type: Types::STRING, length: 4, unique: true)]
    #[Assert\NotBlank(message: 'Publisher ID cannot be blank.')]
    #[Assert\Length(
        min: 4,
        max: 4,
        exactMessage: 'Publisher ID must be exactly {{ limit }} characters.'
    )]
    private string $pubId;

    #[ORM\Column(name: 'pub_name', type: Types::STRING, length: 40, nullable: true)]
    #[Assert\Length(max: 40)]
    private ?string $pubName = null;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $city = null;

    #[ORM\Column(name: 'state', type: Types::STRING, length: 2, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 2,
        exactMessage: 'State must be exactly {{ limit }} characters.'
    )]
    private ?string $state = null;

    #[ORM\Column(name: 'country', type: Types::STRING, length: 30, nullable: true, options: ['default' => 'USA'])]
    #[Assert\Length(max: 30)]
    private ?string $country = 'USA';

    public function getPubId(): string
    {
        return $this->pubId;
    }

    public function setPubId(string $pubId): self
    {
        $this->pubId = $pubId;

        return $this;
    }

    public function getPubName(): ?string
    {
        return $this->pubName;
    }

    public function setPubName(?string $pubName): self
    {
        $this->pubName = $pubName;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function __toString(): string
    {
        return $this->pubName ?? $this->pubId;
    }
}
