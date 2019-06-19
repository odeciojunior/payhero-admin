<div class='page-content container-fluid'>
    <table class='table-hover' style='width: 100%'>
        <tbody>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Tipo</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$shipping->type == 'static'? 'Estatico':'Calculado automaticamente'}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Descrição</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$shipping->name}}</td>
                <br>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>valor</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$shipping->value}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Informação</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$shipping->information}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Status</th>
                <td style='width: 20px'></td>
                <td class='text-left'>
                    @if($shipping->status == 1)
                        <span class='badge badge-success text-left'>Ativo</span>
                    @else
                        <span class='badge badge-danger'>Desativado</span>
                    @endif
                </td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Pré Selecionado</th>
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
