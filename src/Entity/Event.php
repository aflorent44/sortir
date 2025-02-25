<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\EventStatus;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la sortie doit être renseignée.")]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de début de la sortie doit être renseignée.")]
    #[Assert\GreaterThan("today", message: "La date de début de la sortie doit être future.")]
    private ?\DateTimeImmutable $beginsAt = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de fin de la sortie doit être renseignée.")]
    #[Assert\GreaterThan(propertyPath: "beginsAt", message: "La date de fin de la sortie doit être après la date de début.")]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\Column]
    private ?\DateInterval $duration = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de fin des inscriptions doit être renseignée.")]
    #[Assert\GreaterThan("today", message: "La date de fin des inscriptions doit être future.")]
    #[Assert\LessThan(propertyPath: "beginsAt", message: "La date de fin des inscription doit être antérieure à la date de début de la sortie.")]
    private ?\DateTimeImmutable $registrationEndsAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $maxParticipantNumber = null;

    /**
     * @var Collection<int, Campus>
     */
    #[ORM\ManyToMany(targetEntity: Campus::class, inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $campuses;

    #[ORM\Column(enumType: EventStatus::class)]
    private EventStatus $status;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $host = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'events')]
    private Collection $participants;

    public function __construct()
    {
        $this->campuses = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->status = EventStatus::CREATED;
    }

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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setDuration(): void
    {
        if ($this->beginsAt !== null && $this->endsAt !== null) {
            $this->duration = $this->beginsAt->diff($this->endsAt);
        }
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

    public function getMaxParticipantNumber(): ?int
    {
        return $this->maxParticipantNumber;
    }

    public function setMaxParticipantNumber(int $maxParticipantNumber): static
    {
        $this->maxParticipantNumber = $maxParticipantNumber;

        return $this;
    }

    public function getStatus(): EventStatus
    {
        return $this->status;
    }

    public function setStatus(EventStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection<int, Campus>
     */
    public function getCampuses(): Collection
    {
        return $this->campuses;
    }

    public function addCampus(Campus $campus): static
    {
        if (!$this->campuses->contains($campus)) {
            $this->campuses->add($campus);
        }

        return $this;
    }

    public function removeCampus(Campus $campus): static
    {
        $this->campuses->removeElement($campus);

        return $this;
    }

    public function getHost(): ?User
    {
        return $this->host;
    }

    public function setHost(?User $host): static
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }
}
