<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table(name: 'jobs')]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'job_id', type: Types::SMALLINT)]
    private ?int $jobId = null;

    #[ORM\Column(
        name: 'job_desc',
        type: Types::STRING,
        length: 50,
        options: ['default' => 'New Position - title not formalized yet']
    )]
    #[Assert\NotBlank(message: 'İş tanımı boş olamaz')]
    #[Assert\Length(max: 50, maxMessage: 'İş tanımı en fazla {{ limit }} karakter olabilir')]
    private string $jobDesc = 'New Position - title not formalized yet';

    #[ORM\Column(
        name: 'min_lvl',
        type: Types::SMALLINT,
        options: ['check' => 'min_lvl >= 10']
    )]
    #[Assert\NotNull(message: 'Minimum seviye boş olamaz')]
    #[Assert\Range(min: 10, max: 250, notInRangeMessage: 'Minimum seviye {{ min }} ile {{ max }} arasında olmalıdır')]
    private int $minLvl;

    #[ORM\Column(
        name: 'max_lvl',
        type: Types::SMALLINT,
        options: ['check' => 'max_lvl <= 250']
    )]
    #[Assert\NotNull(message: 'Maximum seviye boş olamaz')]
    #[Assert\Range(min: 10, max: 250, notInRangeMessage: 'Maximum seviye {{ min }} ile {{ max }} arasında olmalıdır')]
    private int $maxLvl;

    // 🧩 Getter / Setter Metodları

    public function getJobId(): ?int
    {
        return $this->jobId;
    }

    public function getJobDesc(): string
    {
        return $this->jobDesc;
    }

    public function setJobDesc(string $jobDesc): self
    {
        $this->jobDesc = $jobDesc;

        return $this;
    }

    public function getMinLvl(): int
    {
        return $this->minLvl;
    }

    public function setMinLvl(int $minLvl): self
    {
        $this->minLvl = $minLvl;

        return $this;
    }

    public function getMaxLvl(): int
    {
        return $this->maxLvl;
    }

    public function setMaxLvl(int $maxLvl): self
    {
        $this->maxLvl = $maxLvl;

        return $this;
    }

    public function __toString(): string
    {
        return $this->jobDesc;
    }
}
