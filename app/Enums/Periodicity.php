<?php

declare(strict_types = 1);

namespace App\Enums;

enum Periodicity: string
{
    case Mensal     = 'mensal';
    case Trimestral = 'trimestral';
    case Anual      = 'anual';
}
