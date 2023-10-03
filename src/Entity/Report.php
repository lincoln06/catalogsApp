<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReportCategory $category = null;

    #[ORM\Column(length: 60)]
    private ?string $reportFrom = null;

    #[ORM\Column(length: 100)]
    private ?string $topic = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?ReportCategory
    {
        return $this->category;
    }

    public function setCategory(?ReportCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getReportFrom(): ?string
    {
        return $this->reportFrom;
    }

    public function setReportFrom(string $reportFrom): static
    {
        $this->reportFrom = $reportFrom;

        return $this;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): static
    {
        $this->topic = $topic;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
