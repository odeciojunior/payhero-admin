<div class='page-content container-fluid'>
    <table class='table-hover' style='width: 100%;'>
        <tbody>
            <tr style='height: 40%'>
                <th style='height: 40%' class='text-center'>Nome:</th>
                <td style='height: 40%'></td>
                <td class='text-left'>{{$user->name ?? ''}}</td>
            </tr>
            <tr style='height: 40%'>
                <th style='height: 40%' class='text-center'>Tipo de parceiro:</th>
                <td style='height: 40%'></td>
                <td class='text-left'>{{$partner->type == 'partner' ? 'Sócio' : 'Produtor'}}</td>
            </tr>
            <tr style='height: 40%'>
                <th style='height: 40%' class='text-center'>Status:</th>
                <td style='height: 40%'></td>
                <td class='text-left'>{{$partner->status == 'active' ? 'Ativado' : 'Desativado'}}</td>
            </tr>
            <tr style='height: 40%'>
                <th style='height: 40%' class='text-center'>Valor da Remuneração</th>
                <td style='height: 40%'></td>
                <td class='text-left'>{{$partner->remuneration_value}}</td>
            </tr>
            <tr style='height: 40%'>
                <th style='height: 40%' class='text-center'>Permissão de acesso aos dados do projeto:</th>
                <td style='height: 40%'></td>
                <td class='text-left'>
                    @if($partner->access_permission == 1)
                        <span class='badge badge-success'>Sim</span>
                    @else
                        <span class='badge badge-primary'>Não</span>
                    @endif
                </td>
            </tr>
            <tr style='height: 40%'>
                <th style='height: 40%' class='text-center'>Permissão de editar os dados do projeto:</th>
                <td style='height: 40%'></td>
                <td class='text-left'>
                    @if($partner->edit_permission == 1)
                        <span class='badge badge-success'>Sim</span>
                    @else
                        <span class='badge badge-primary'>Não</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
