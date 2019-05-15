<div class='col-xl-12 col-lg-12'>
    <table class='table table-bordered table-hover table-striped'>
        <thead>
        </thead>
        <tbody>
            <tr>
                <td><b>Nome:</b></td>
                <td>{!! $user['name'] !!}</td>
            </tr>
            <tr>
                <td><b>Tipo de parceiro:</b></td>
                <td>{!! $parceiro['tipo'] !!}</td>
            </tr>
            <tr>
                <td><b>Status:</b></td>
                <td>{!! $parceiro['status'] !!}</td>
            </tr>
            <tr>
                <td><b>Tipo de remuneração:</b></td>
                <td>{!! $parceiro['tipo_remuneracao'] !!}</td>
            </tr>
            <tr>
                <td><b>Valor da remuneração:</b></td>
                <td>{!! $parceiro['valor_remuneracao'] !!}</td>
            </tr>
            <tr>
                <td><b>Permissão de acesso aos dados do projeto:</b></td>
                <td>{!! $parceiro['permissao_acesso'] ? 'Sim' : 'Não' !!}</td>
            </tr>
            <tr>
                <td><b>Permissão de editar os dados do projeto:</b></td>
                <td>{!! $parceiro['permissao_editar'] ? 'Sim' : 'Não' !!}</td>
            </tr>
        </tbody>
    </table>
</div>

