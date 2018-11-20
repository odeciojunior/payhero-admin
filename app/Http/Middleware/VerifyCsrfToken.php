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
        '/produtos/data-source',
        '/produtos/getprodutos',
        '/produtos/addprodutoprojeto',
        '/produtos/deletarprodutoplano',
        '/dominios/getformadddominio',
        '/projetos/data-source',
        '/pixels/cadastrarpixel',
        '/pixels/getformeditarpixel',
        '/pixels/editarpixel',
        '/brindes/data-source',
        '/cuponsdesconto/data-source',
        '/despachos/data-source',
        '/transportadoras/data-source',
        '/tiposbrindes/data-source',
        '/pixels/data-source',
        '/pixels/getformaddpixel',
        '/categorias/data-source',
        '/planos/data-source',
        '/dominios/data-source',
        '/layouts/data-source',
        '/categorias/detalhe',
        '/cuponsdesconto/detalhe',
        '/transportadoras/detalhe',
        '/despachos/detalhe',
        '/brindes/detalhe',
        '/pixels/detalhe',
        '/planos/detalhe',
        '/projetos/detalhe',
        '/produtos/detalhe',
        '/empresas/detalhe',
        '/relatorios/venda/detalhe',
        '/usuarios/detalhe',
        '/despachos/codigorastreio',
        '/despachos/addcodigorastreio',
        '/liftgold',
    ];
}
