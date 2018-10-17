<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/relatorios/vendas/data-source',
        '/logs/data-source',
        '/usuarios/data-source',
        '/empresas/data-source',
        '/empresas/detalhe',
        '/relatorios/venda/detalhe',
        '/usuarios/detalhe',
    ];
}
