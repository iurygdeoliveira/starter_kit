<?php

declare(strict_types = 1);

namespace App\Enums;

enum Permission: string
{
    case Apagar     = 'Apagar';
    case Criar      = 'Criar';
    case Editar     = 'Editar';
    case Visualizar = 'Visualizar';
}
