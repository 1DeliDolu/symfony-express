<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table(name: 'jobs')]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'job_id', type: 'smallint')]
    private ?int $jobId = null;

    #[ORM\Column(
        name: 'job_desc',
        type: 'string',
        length: 50,
        options: ['default' => 'New Position - title not formalized yet']
    )]
    private string $jobDesc = 'New Position - title not formalized yet';

    #[ORM\Column(
        name: 'min_lvl',
        type: 'smallint',
        options: ['check' => 'min_lvl >= 10']
    )]
    private int $minLvl;

    #[ORM\Column(
        name: 'max_lvl',
        type: 'smallint',
        options: ['check' => 'max_lvl <= 250']
    )]
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
}
