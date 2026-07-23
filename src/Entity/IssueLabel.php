<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\IssueLabelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IssueLabelRepository::class)]
#[ORM\HasLifecycleCallbacks]
class IssueLabel
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Issue::class, inversedBy: 'issueLabels')]
    #[ORM\JoinColumn(nullable: false)]
    private Issue $issue;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Label::class, inversedBy: 'issueLabels')]
    #[ORM\JoinColumn(nullable: false)]
    private Label $label;

    public function __construct(Issue $issue, Label $label)
    {
        $this->issue = $issue;
        $this->label = $label;
    }

    public function getIssue(): Issue
    {
        return $this->issue;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }
}
