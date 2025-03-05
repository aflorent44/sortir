<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé.')]
#[UniqueEntity(fields: ['phoneNumber'], message: 'Ce numéro de téléphone est déjà utilisé.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 200, unique: true)]
    #[Assert\Email(message: 'Votre email {{ value }} est incorrect.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9._%+-]+@campus-eni\.fr$/',
        message: 'Vous devez utiliser votre adresse @campus-eni.fr'
    )]
    #[Assert\NotBlank]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire.')]
    #[Assert\Length(min: 3, max: 50,
        minMessage: 'Votre nom doit contenir minimum {{ limit }} caractères.',
        maxMessage: 'Votre nom doit contenir maximum {{ limit }} caractères.')]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire.')]
    #[Assert\Length(min: 3, max: 50,
        minMessage: 'Votre prénom doit contenir minimum {{ limit }} caractères.',
        maxMessage: 'Votre prénom doit contenir maximum {{ limit }} caractères.')]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^0[67]([-. ]?[0-9]{2}){4}$/',
        message: 'Le numéro de téléphone doit commencer par 06 ou 07 et être au format valide.'
    )]
    private ?string $phoneNumber = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'host')]
    private Collection $events;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire.')]
    #[Assert\Length(min: 3, max: 50,
        minMessage: 'Votre pseudo doit contenir minimum {{ limit }} caractères.',
        maxMessage: 'Votre pseudo doit contenir maximum {{ limit }} caractères.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9._-]+$/',
        message: 'Le pseudo ne peut contenir que des lettres, chiffres et les symboles "-", "_" et ".".'
    )]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\Column]
    private ?bool $isActive = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $activationToken = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $activationTokenCreatedAt = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->profileImage = 'user1.png';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setHost($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getHost() === $this) {
                $event->setHost(null);
            }
        }

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        // Si profileImage est null ou égal à 'user1.png', on ne retourne rien
        // ce qui forcera le template à utiliser l'image par défaut
        if ($this->profileImage === 'user1.png' || $this->profileImage === null) {
            return null;
        }
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): static
    {
        $this->activationToken = $activationToken;
        return $this;
    }

    public function getActivationTokenCreatedAt(): ?\DateTimeImmutable
    {
        return $this->activationTokenCreatedAt;
    }

    public function setActivationTokenCreatedAt(?\DateTimeImmutable $activationTokenCreatedAt): static
    {
        $this->activationTokenCreatedAt = $activationTokenCreatedAt;

        return $this;
    }

}
