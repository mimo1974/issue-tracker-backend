<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\AuthProvider;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\AuthIdentityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthIdentityRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(name: 'uniq_auth_identity_provider_provider_user_id', columns: ['provider', 'provider_user_id'])]
#[ORM\UniqueConstraint(name: 'uniq_auth_identity_provider_email', columns: ['provider', 'email'])]
#[ORM\HasLifecycleCallbacks]
class AuthIdentity
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: AuthProvider::class)]
    private AuthProvider $provider;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $providerUserId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passwordHash = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'authIdentities')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    public function __construct(User $user, AuthProvider $provider)
    {
        $this->user = $user;
        $this->provider = $provider;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): AuthProvider
    {
        return $this->provider;
    }

    public function setProvider(AuthProvider $provider): void
    {
        $this->provider = $provider;
    }

    public function getProviderUserId(): ?string
    {
        return $this->providerUserId;
    }

    public function setProviderUserId(?string $providerUserId): void
    {
        $this->providerUserId = $providerUserId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(?string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(): void
    {
        $this->lastLoginAt = new \DateTimeImmutable();
    }
}
