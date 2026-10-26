<?php

declare(strict_types=1);

namespace Vanilo\Support\Models;

enum RoundingLevel: string
{
    case Line = 'line';
    case Aggregate = 'aggregate';
    case Any = '*';
}
