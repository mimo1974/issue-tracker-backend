<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Label
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'labels')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    /** @var Collection<int, IssueLabel> */
    #[ORM\OneToMany(targetEntity: IssueLabel::class, mappedBy: 'label')]
    private Collection $issueLabels;

    public function __construct(Project $project, string $name)
    {
        $this->project = $project;
        $this->name = $name;
        $this->issueLabels = new ArrayCollection();
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

    public function getProject(): Project
    {
        return $this->project;
    }

    /** @return Collection<int, IssueLabel> */
    public function getIssueLabels(): Collection
    {
        return $this->issueLabels;
    }
}
