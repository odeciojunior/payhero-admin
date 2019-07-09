    <table class='table' style='width: 100%'>
        <tbody>
            <tr >
                <td  class="table-title">Tipo</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$shipping->type == 'static'? 'Estatico': $shipping->type == 'pac'? 'PAC - Caculado automaticamente' : 'SEDEX - Caculado automaticamente'}}</td>
            </tr>
            <tr >
                <td  class="table-title">Descrição</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$shipping->name}}</td>
                <br>
            </tr>
            <tr >
                <td  class="table-title">Valor</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$shipping->value == null? ' Calculado automaticamente' : $shipping->value}}</td>
            </tr>
            <tr >
                <td  class="table-title">Informação</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$shipping->information}}</td>
            </tr>
            <tr >
                <td  class="table-title">Status</th>
                <td style='width: 20px'></td>
                <td class='text-left'>
                    @if($shipping->status == 1)
                        <span class='badge badge-success text-left'>Ativo</span>
                    @else
                        <span class='badge badge-danger'>Desativado</span>
                    @endif
                </td>
            </tr>
            <tr >
                <td  class="table-title">Pré Selecionado</th>
                <td style='width: 20px'></td>
                <td class='text-left'>
                    @if($shipping->pre_selected == 1)
                        <span class='badge badge-success'>Sim</span>
                    @else
                        <span class='badge badge-primary'>Não</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
