<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum IssueStatus: string
{
    case Backlog = 'backlog';
    case Todo = 'todo';
    case InProgress = 'in-progress';
    case InReview = 'in-review';
    case Done = 'done';
}
