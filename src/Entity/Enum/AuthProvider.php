<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum AuthProvider: string
{
    case Google = 'google';
    case Email = 'email';
}
