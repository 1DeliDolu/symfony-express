<?php

declare(strict_types=1);

namespace App\Entity\Pubs;

use App\Repository\Pubs\PubInfoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PubInfoRepository::class)]
#[ORM\Table(name: 'pub_info')]
class PubInfo
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Publisher::class)]
    #[ORM\JoinColumn(name: 'pub_id', referencedColumnName: 'pub_id', nullable: false)]
    #[Assert\NotNull(message: 'Yayıncı seçilmelidir')]
    private Publisher $publisher;

    #[ORM\Column(name: 'logo', type: Types::BLOB, nullable: true)]
    private $logo;

    #[ORM\Column(name: 'pr_info', type: Types::TEXT, nullable: true)]
    private ?string $prInfo = null;

    // 🧩 Getter / Setter Metodları

    public function getPublisher(): Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(Publisher $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getPrInfo(): ?string
    {
        return $this->prInfo;
    }

    public function setPrInfo(?string $prInfo): self
    {
        $this->prInfo = $prInfo;

        return $this;
    }
}
