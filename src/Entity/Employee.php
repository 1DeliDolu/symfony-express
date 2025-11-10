<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\Table(name: 'employee')]
class Employee
{
    #[ORM\Id]
    #[ORM\Column(name: 'emp_id', type: Types::STRING, length: 9, unique: true)]
    #[Assert\NotBlank(message: 'Çalışan ID boş olamaz')]
    #[Assert\Regex(
        pattern: '/^[A-Z]{3}-[A-Z]{1}\d{4}$|^\d{9}$/',
        message: 'Çalışan ID formatı XXX-XXXXX veya 9 rakam olmalıdır'
    )]
    private string $empId;

    #[ORM\Column(name: 'fname', type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: 'Ad boş olamaz')]
    #[Assert\Length(max: 20, maxMessage: 'Ad en fazla {{ limit }} karakter olabilir')]
    private string $fname;

    #[ORM\Column(name: 'minit', type: Types::STRING, length: 1, nullable: true)]
    #[Assert\Length(max: 1, maxMessage: 'Orta isim en fazla {{ limit }} karakter olabilir')]
    private ?string $minit = null;

    #[ORM\Column(name: 'lname', type: Types::STRING, length: 30)]
    #[Assert\NotBlank(message: 'Soyad boş olamaz')]
    #[Assert\Length(max: 30, maxMessage: 'Soyad en fazla {{ limit }} karakter olabilir')]
    private string $lname;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(name: 'job_id', referencedColumnName: 'job_id', nullable: false, options: ['default' => 1])]
    #[Assert\NotNull(message: 'İş pozisyonu seçilmelidir')]
    private Job $job;

    #[ORM\Column(name: 'job_lvl', type: Types::SMALLINT, nullable: true, options: ['default' => 10])]
    #[Assert\Range(min: 10, max: 250, notInRangeMessage: 'İş seviyesi {{ min }} ile {{ max }} arasında olmalıdır')]
    private ?int $jobLvl = 10;

    #[ORM\ManyToOne(targetEntity: Publisher::class)]
    #[ORM\JoinColumn(name: 'pub_id', referencedColumnName: 'pub_id', nullable: false, options: ['default' => '9952'])]
    #[Assert\NotNull(message: 'Yayıncı seçilmelidir')]
    private Publisher $publisher;

    #[ORM\Column(name: 'hire_date', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\NotNull(message: 'İşe başlama tarihi boş olamaz')]
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
