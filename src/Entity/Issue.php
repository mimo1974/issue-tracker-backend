<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\IssuePriority;
use App\Entity\Enum\IssueStatus;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\IssueRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IssueRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(name: 'uniq_issue_project_number', columns: ['project_id', 'number'])]
#[ORM\HasLifecycleCallbacks]
class Issue
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Per-project sequence number. Combined with the Project's `key`
     * (e.g. `COR`) to form the issue's public identifier, e.g. `COR-123`.
     */
    #[ORM\Column]
    private int $number;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(enumType: IssueStatus::class)]
    private IssueStatus $status;

    #[ORM\Column(enumType: IssuePriority::class)]
    private IssuePriority $priority;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $dueDate = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'issues')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'assignedIssues')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $assignee = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reportedIssues')]
    #[ORM\JoinColumn(nullable: false)]
    private User $reporter;

    /** @var Collection<int, IssueLabel> */
    #[ORM\OneToMany(targetEntity: IssueLabel::class, mappedBy: 'issue')]
    private Collection $issueLabels;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'issue')]
    private Collection $comments;

    public function __construct(
        Project $project,
        int $number,
        string $title,
        User $reporter,
        IssueStatus $status = IssueStatus::Backlog,
        IssuePriority $priority = IssuePriority::Medium,
    ) {
        $this->project = $project;
        $this->number = $number;
        $this->title = $title;
        $this->reporter = $reporter;
        $this->status = $status;
        $this->priority = $priority;
        $this->issueLabels = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getKey(): string
    {
        return sprintf('%s-%d', $this->project->getKey(), $this->number);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getStatus(): IssueStatus
    {
        return $this->status;
    }

    public function setStatus(IssueStatus $status): void
    {
        $this->status = $status;
    }

    public function getPriority(): IssuePriority
    {
        return $this->priority;
    }

    public function setPriority(IssuePriority $priority): void
    {
        $this->priority = $priority;
    }

    public function getDueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTimeImmutable $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setAssignee(?User $assignee): void
    {
        $this->assignee = $assignee;
    }

    public function getReporter(): User
    {
        return $this->reporter;
    }

    /** @return Collection<int, IssueLabel> */
    public function getIssueLabels(): Collection
    {
        return $this->issueLabels;
    }

    /** @return Collection<int, Comment> */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
