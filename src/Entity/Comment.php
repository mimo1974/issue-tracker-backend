<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $body;

    #[ORM\ManyToOne(targetEntity: Issue::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private Issue $issue;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    public function __construct(Issue $issue, User $author, string $body)
    {
        $this->issue = $issue;
        $this->author = $author;
        $this->body = $body;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getIssue(): Issue
    {
        return $this->issue;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }
}
