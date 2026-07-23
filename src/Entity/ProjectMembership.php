<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\ProjectRole;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\ProjectMembershipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectMembershipRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(name: 'uniq_project_membership_user_project', columns: ['user_id', 'project_id'])]
#[ORM\HasLifecycleCallbacks]
class ProjectMembership
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: ProjectRole::class)]
    private ProjectRole $role;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'projectMemberships')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    public function __construct(User $user, Project $project, ProjectRole $role)
    {
        $this->user = $user;
        $this->project = $project;
        $this->role = $role;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ProjectRole
    {
        return $this->role;
    }

    public function setRole(ProjectRole $role): void
    {
        $this->role = $role;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
