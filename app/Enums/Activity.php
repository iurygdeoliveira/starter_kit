<?php

declare(strict_types = 1);

namespace App\Enums;

enum Activity: string
{
    case Comercio        = 'Comércio';
    case Industria       = 'Indústria';
    case Servicos        = 'Serviços';
    case ComercioServico = 'Comércio/Serviço';
}
