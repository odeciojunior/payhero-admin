<div class='container-fluid'>
    <table class='table table-bordered table-striped table-hover' style='overflow-x: auto !important;'>
        <tbody>
            <tr>
                <th style='width:40%;' class='text-center'>Nome:</th>
                <td class='text-left' id='plan_name_details'></td>
                <br>
            </tr>
            <tr>
                <th style='width:40%;' class='text-center'>Descrição:</th>
                <td class='text-left' id='plan_description_details'></td>
            </tr>
            <tr>
                <th style='width:40%;' class='text-center'>Link:</th>
                <td class='text-left' id='plan_code_edit_details'></td>
            </tr>
            <tr>
                <th style='width:40%;' class='text-center'>Preço:</th>
                <td class='text-left' id='plan_price_edit_details'></td>
            </tr>
            <tr>
                <th style='width:40%;' class='text-center'>Status:</th>
                <td class='text-left' id='plan_status_edit_details'>
                </td>
            </tr>
        </tbody>
    </table>
    <table class='table table-bordered table-striped table-hover mt-2 text-center'>
        <thead>
            <th>Produto:</th>
            <th>Quantidade:</th>
        </thead>
        <tbody id='products_plan_details'>
            {{-- tr carregado no js--}}
        </tbody>
    </table>
</div>

