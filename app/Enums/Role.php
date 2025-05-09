<?php

declare(strict_types = 1);

namespace App\Enums;

enum Role: string
{
    case Fiscal        = 'Fiscal';
    case Contabil      = 'Contábil';
    case Portal        = 'Portal do Cliente';
    case Cliente       = 'Cliente';
    case Administração = 'Administração';
    case CND           = 'Certidão Negativa de Débito (CND)';
    case Pessoal       = 'Pessoal';
    case Financeiro    = 'Financeiro';
    case Processos     = 'Processos';
    case Suporte       = 'Suporte';
    case Dashboard     = 'Painel de Controle';
}
