<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    use Timestampable;

    public const TOKEN_VALIDITY = '-4 hours';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string|null The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $confirmationToken = null;

    /**
     * @ORM\Column(type="boolean", options={"default" : "0"})
     */
    private bool $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $passwordResetToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    final public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    final public function getPassword(): string
    {
        return (string) $this->password;
    }

    final public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    final public function getSalt(): void
    {
    }

    /**
     * @see UserInterface
     */
    final public function eraseCredentials(): void
    {
    }

    final public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    final public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    final public function isVerified(): bool
    {
        return $this->isVerified;
    }

    final public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    final public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    final public function setPasswordResetToken(?string $passwordResetToken): self
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }
}
