<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $beginsAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\Column]
    private ?\DateInterval $duration = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $registrationEndsAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $maxParticipantNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBeginsAt(): ?\DateTimeImmutable
    {
        return $this->beginsAt;
    }

    public function setBeginsAt(\DateTimeImmutable $beginsAt): static
    {
        $this->beginsAt = $beginsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeImmutable $endsAt): static
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(\DateInterval $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationEndsAt(): ?\DateTimeImmutable
    {
        return $this->registrationEndsAt;
    }

    public function setRegistrationEndsAt(\DateTimeImmutable $registrationEndsAt): static
    {
        $this->registrationEndsAt = $registrationEndsAt;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMaxParticipantNumber(): ?int
    {
        return $this->maxParticipantNumber;
    }

    public function setMaxParticipantNumber(int $maxParticipantNumber): static
    {
        $this->maxParticipantNumber = $maxParticipantNumber;

        return $this;
    }
}
