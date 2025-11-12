<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\TerritoriesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TerritoriesRepository::class)]
#[ORM\Table(name: "Territories", schema: "dbo")]
class Territories
{
    #[ORM\Id]
    #[ORM\Column(name: "TerritoryID", length: 20)]
    private ?string $territoryId = null;

    #[ORM\Column(name: "TerritoryDescription", length: 50)]
    private ?string $territoryDescription = null;

    #[ORM\Column(name: "RegionID", type: "integer")]
    private ?int $regionId = null;

    public function getTerritoryId(): ?string
    {
        return $this->territoryId;
    }

    public function setTerritoryId(string $territoryId): static
    {
        $this->territoryId = $territoryId;

        return $this;
    }

    public function getTerritoryDescription(): ?string
    {
        return $this->territoryDescription;
    }

    public function setTerritoryDescription(string $territoryDescription): static
    {
        $this->territoryDescription = $territoryDescription;

        return $this;
    }

    public function getRegionId(): ?int
    {
        return $this->regionId;
    }

    public function setRegionId(int $regionId): static
    {
        $this->regionId = $regionId;

        return $this;
    }
}
