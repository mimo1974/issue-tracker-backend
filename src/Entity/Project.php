<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Project
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 16, unique: true)]
    private string $key;

    /** @var Collection<int, ProjectMembership> */
    #[ORM\OneToMany(targetEntity: ProjectMembership::class, mappedBy: 'project')]
    private Collection $memberships;

    /** @var Collection<int, Issue> */
    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'project')]
    private Collection $issues;

    /** @var Collection<int, Label> */
    #[ORM\OneToMany(targetEntity: Label::class, mappedBy: 'project')]
    private Collection $labels;

    public function __construct(string $name, string $key)
    {
        $this->name = $name;
        $this->key = $key;
        $this->memberships = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->labels = new ArrayCollection();
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

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /** @return Collection<int, ProjectMembership> */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    /** @return Collection<int, Issue> */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    /** @return Collection<int, Label> */
    public function getLabels(): Collection
    {
        return $this->labels;
    }
}
