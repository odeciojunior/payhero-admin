<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class LayoutController extends Controller {

    public function getMenuLateral() {

        $menu = [];

        $menu[] = 'Dashboard';
        $menu[] = 'Vitrine';
        $menu[] = [
            'Vendas' => [
                'Visão geral',
                'Recuperação de carrinho',
                'Central de reembolso'
            ]
        ];
        $menu[] = [ 
            'Projetos' => [
                'Cadastrar projeto',
                'Meus projetos'
            ]
        ];
        $menu[] = [
            'Produtos' => [
                'Cadastrar produtos',
                'Meus produtos'
            ]
        ];
        $menu[] = [
            'Atendimento' => [
                'Visão geral',
                'Configurações'
            ]
        ];
        $menu[] = [
            'Afiliados' => [
                'Minhas afiliações',
                'Meus afiliados'
            ]        
        ];
        $menu[] = [
            'Finanças' => [
                'Extrato',
                'Transferências'
            ]
        ];
        $menu[] = 'Ferramentas';
        $menu[] = 'Aplicativos';

        return response()->json([
            'menu' => $menu
        ]);


    }

}
