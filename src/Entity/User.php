<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 8)]
    private string $initials;

    /** @var Collection<int, AuthIdentity> */
    #[ORM\OneToMany(targetEntity: AuthIdentity::class, mappedBy: 'user')]
    private Collection $authIdentities;

    /** @var Collection<int, ProjectMembership> */
    #[ORM\OneToMany(targetEntity: ProjectMembership::class, mappedBy: 'user')]
    private Collection $projectMemberships;

    /** @var Collection<int, Issue> */
    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'assignee')]
    private Collection $assignedIssues;

    /** @var Collection<int, Issue> */
    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'reporter')]
    private Collection $reportedIssues;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author')]
    private Collection $comments;

    public function __construct(string $name, string $initials)
    {
        $this->name = $name;
        $this->initials = $initials;
        $this->authIdentities = new ArrayCollection();
        $this->projectMemberships = new ArrayCollection();
        $this->assignedIssues = new ArrayCollection();
        $this->reportedIssues = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getInitials(): string
    {
        return $this->initials;
    }

    public function setInitials(string $initials): void
    {
        $this->initials = $initials;
    }

    /** @return Collection<int, AuthIdentity> */
    public function getAuthIdentities(): Collection
    {
        return $this->authIdentities;
    }

    /** @return Collection<int, ProjectMembership> */
    public function getProjectMemberships(): Collection
    {
        return $this->projectMemberships;
    }

    /** @return Collection<int, Issue> */
    public function getAssignedIssues(): Collection
    {
        return $this->assignedIssues;
    }

    /** @return Collection<int, Issue> */
    public function getReportedIssues(): Collection
    {
        return $this->reportedIssues;
    }

    /** @return Collection<int, Comment> */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /** @return list<string> */
    #[\Override]
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        if (null === $this->id) {
            throw new \LogicException('Cannot get the user identifier of a User that has not been persisted yet.');
        }

        return (string) $this->id;
    }
}
