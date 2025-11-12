<?php

namespace App\Entity\Northwind;

use App\Repository\Northwind\CategoriesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
#[ORM\Table(name: "Categories", schema: "dbo")]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "CategoryID", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "CategoryName", length: 15)]
    private ?string $categoryName = null;

    #[ORM\Column(name: "Description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "Picture", type: Types::BLOB, nullable: true)]
    private $picture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): static
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture): static
    {
        $this->picture = $picture;

        return $this;
    }
}
