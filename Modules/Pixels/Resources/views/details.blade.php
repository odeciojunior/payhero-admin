<div class='page-content container-fluid'>
    <table class='table-hover' style='width: 100%'>
        <tbody>
            {{--<tr style='height: 40%;'>--}}
            {{--<th style='width:40%;' class='text-center'>Tipo</th>--}}
            {{--<td style='width: 20px'></td>--}}
            {{--<td class='text-left'>{{$pixel->type == 'static'? 'Estatico': $shipping->type == 'pac'? 'PAC - Caculado automaticamente' : 'SEDEX - Caculado automaticamente'}}</td>--}}
            {{--</tr>--}}
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Descrição</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$pixel->name}}</td>
                <br>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Code</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$pixel->code}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Plataforma</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$pixel->platform}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Status</th>
                <td style='width: 20px'></td>
                <td class='text-left'>
                    @if($pixel->status == 1)
                        <span class='badge badge-success text-left'>Ativo</span>
                    @else
                        <span class='badge badge-danger'>Desativado</span>
                    @endif
                </td>
            </tr>
            {{--<tr style='height: 40%;'>--}}
            {{--<th style='width:40%;' class='text-center'>Informação</th>--}}
            {{--<td style='width: 20px'></td>--}}
            {{--<td class='text-left'>{{$shipping->information}}</td>--}}
            {{--</tr>--}}

            {{--<tr style='height: 40%;'>--}}
            {{--<th style='width:40%;' class='text-center'>Pré Selecionado</th>--}}
            {{--<td style='width: 20px'></td>--}}
            {{--<td class='text-left'>--}}
            {{--@if($shipping->pre_selected == 1)--}}
            {{--<span class='badge badge-success'>Sim</span>--}}
            {{--@else--}}
            {{--<span class='badge badge-primary'>Não</span>--}}
            {{--@endif--}}
            {{--</td>--}}
            {{--</tr>--}}
        </tbody>
    </table>
</div>
