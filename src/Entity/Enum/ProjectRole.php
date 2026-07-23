<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum ProjectRole: string
{
    case Admin = 'admin';
    case Member = 'member';
}
