<div class='page-content container-fluid'>
    <table class='table-hover' style='width: 100%'>
        <tbody>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Nome</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$plan->name}}</td>
                <br>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Descrição</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$plan->description}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Código</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$plan->code}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Preço</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$plan->price}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Produtos</th>
                <td style='width: 20px'></td>
                @foreach($plan->productsPlans as $productPlan)
                    <td class='text-left'>{{$productPlan->getProduct->name}}</td>
                @endforeach
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Status</th>
                <td style='width: 20px'></td>
                <td class='text-left'>
                    @if($plan->status == 1)
                        <span class='badge badge-success text-left'>Ativo</span>
                    @else
                        <span class='badge badge-danger'>Desativado</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
