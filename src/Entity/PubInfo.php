<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Publisher;

#[ORM\Entity(repositoryClass: 'App\Repository\PubInfoRepository')]
#[ORM\Table(name: 'pub_info')]
class PubInfo
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Publisher::class)]
    #[ORM\JoinColumn(name: 'pub_id', referencedColumnName: 'pub_id', nullable: false)]
    private Publisher $publisher;

    #[ORM\Column(name: 'logo', type: 'blob', nullable: true)]
    private $logo = null;

    #[ORM\Column(name: 'pr_info', type: 'text', nullable: true)]
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
