<?php

declare(strict_types = 1);

namespace App\Enums;

enum Regime: string
{
    case SimplesNacional = 'Simples Nacional';
    case LucroPresumido  = 'Lucro Presumido';
    case LucroReal       = 'Lucro Real';
    case LucroArbitrado  = 'Lucro Arbitrado';
    case Mei             = 'Micro Empreendedor Individual';
}
