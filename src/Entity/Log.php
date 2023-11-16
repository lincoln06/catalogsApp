<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $whoDidTheAction = null;

    #[ORM\Column(length: 100)]
    private ?string $actionType = null;

    #[ORM\Column(length: 100)]
    private ?string $onWhatEntity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $whenActionWasDone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWhoDidTheAction(): ?string
    {
        return $this->whoDidTheAction;
    }

    public function setWhoDidTheAction(string $whoDidTheAction): static
    {
        $this->whoDidTheAction = $whoDidTheAction;

        return $this;
    }

    public function getActionType(): ?string
    {
        return $this->actionType;
    }

    public function setActionType(string $actionType): static
    {
        $this->actionType = $actionType;

        return $this;
    }

    public function getOnWhatEntity(): ?string
    {
        return $this->onWhatEntity;
    }

    public function setOnWhatEntity(string $onWhatEntity): static
    {
        $this->onWhatEntity = $onWhatEntity;

        return $this;
    }

    public function getWhenActionWasDone(): ?\DateTimeInterface
    {
        return $this->whenActionWasDone;
    }

    public function setWhenActionWasDone(\DateTimeInterface $whenActionWasDone): static
    {
        $this->whenActionWasDone = $whenActionWasDone;

        return $this;
    }
}
