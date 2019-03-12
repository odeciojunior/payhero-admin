<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class LayoutController extends Controller {

    public function getMenuLateral() {

        return response()->json([
            'menu' => [
                'Dashboard',
                'Vitrine',
                'Vendas' => [
                    'Visão geral',
                    'Recuperação de carrinho',
                    'Central de reembolso'
                ],
                'Projetos' => [
                    'Cadastrar projeto',
                    'Meus projetos'
                ],
                'Produtos' => [
                    'Cadastrar produtos',
                    'Meus produtos'
                ],
                'Atendimento' => [
                    'Visão geral',
                    'Configurações'
                ],
                'Afiliados' => [
                    'Minhas afiliações',
                    'Meus afiliados'
                ],
                'Finanças' => [
                    'Extrato',
                    'Transferências'
                ],
                'Ferramentas',
                'Aplicativos'
            ] 
        ]);


    }

}
