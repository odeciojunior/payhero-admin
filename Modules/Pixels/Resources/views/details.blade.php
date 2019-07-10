
    <table class='table table-striped' style='width: 100%'>
        <tbody>
            {{--<tr>--}}
            {{--<td class="table-title">Tipo</td>--}}
            {{--<td style='width: 20px'></td>--}}
            {{--<td class='text-left'>{{$pixel->type == 'static'? 'Estatico': $shipping->type == 'pac'? 'PAC - Caculado automaticamente' : 'SEDEX - Caculado automaticamente'}}</td>--}}
            {{--</tr>--}}
            <tr>
                <td class="table-title">Descrição</td>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$pixel->name}}</td>
                <br>
            </tr>
            <tr>
                <td class="table-title">Code</td>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$pixel->code}}</td>
            </tr>
            <tr>
                <td class="table-title">Plataforma</td>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$pixel->platform}}</td>
            </tr>
            <tr>
                <td class="table-title">Status</td>
                <td style='width: 20px'></td>
                <td class='text-left'>
                    @if($pixel->status == 1)
                        <span class='badge badge-success text-left'>Ativo</span>
                    @else
                        <span class='badge badge-danger'>Desativado</span>
                    @endif
                </td>
            </tr>
            {{--<tr>--}}
            {{--<td class="table-title">Informação</td>--}}
            {{--<td style='width: 20px'></td>--}}
            {{--<td class='text-left'>{{$shipping->information}}</td>--}}
            {{--</tr>--}}

            {{--<tr>--}}
            {{--<td class="table-title">Pré Selecionado</td>--}}
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

