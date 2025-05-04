<?php

declare(strict_types = 1);

namespace App\Enums;

enum Role: string
{
    case Fiscal   = 'Fiscal';
    case Contabil = 'Contábil';
    case Portal   = 'Portal do Cliente';
}
