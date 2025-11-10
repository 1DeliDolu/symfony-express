<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Job;
use App\Entity\Publisher;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\Table(name: 'employee')]
class Employee
{
    #[ORM\Id]
    #[ORM\Column(name: 'emp_id', type: 'string', length: 9, unique: true)]
    private string $empId;

    #[ORM\Column(name: 'fname', type: 'string', length: 20)]
    private string $fname;

    #[ORM\Column(name: 'minit', type: 'string', length: 1, nullable: true)]
    private ?string $minit = null;

    #[ORM\Column(name: 'lname', type: 'string', length: 30)]
    private string $lname;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(name: 'job_id', referencedColumnName: 'job_id', nullable: false, options: ['default' => 1])]
    private Job $job;

    #[ORM\Column(name: 'job_lvl', type: 'smallint', nullable: true, options: ['default' => 10])]
    private ?int $jobLvl = 10;

    #[ORM\ManyToOne(targetEntity: Publisher::class)]
    #[ORM\JoinColumn(name: 'pub_id', referencedColumnName: 'pub_id', nullable: false, options: ['default' => '9952'])]
    private Publisher $publisher;

    #[ORM\Column(name: 'hire_date', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $hireDate;

    // 🧩 Getter / Setter Metodları

    public function getEmpId(): string
    {
        return $this->empId;
    }

    public function setEmpId(string $empId): self
    {
        $this->empId = $empId;
        return $this;
    }

    public function getFname(): string
    {
        return $this->fname;
    }

    public function setFname(string $fname): self
    {
        $this->fname = $fname;
        return $this;
    }

    public function getMinit(): ?string
    {
        return $this->minit;
    }

    public function setMinit(?string $minit): self
    {
        $this->minit = $minit;
        return $this;
    }

    public function getLname(): string
    {
        return $this->lname;
    }

    public function setLname(string $lname): self
    {
        $this->lname = $lname;
        return $this;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job): self
    {
        $this->job = $job;
        return $this;
    }

    public function getJobLvl(): ?int
    {
        return $this->jobLvl;
    }

    public function setJobLvl(?int $jobLvl): self
    {
        $this->jobLvl = $jobLvl;
        return $this;
    }

    public function getPublisher(): Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(Publisher $publisher): self
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getHireDate(): \DateTimeInterface
    {
        return $this->hireDate;
    }

    public function setHireDate(\DateTimeInterface $hireDate): self
    {
        $this->hireDate = $hireDate;
        return $this;
    }
}
